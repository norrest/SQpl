import time
import socket
import fcntl
import struct
import subprocess

import Adafruit_SSD1306
from PIL import Image, ImageDraw, ImageFont


def get_ip_address(ifname):
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    name = ifname[:15]
    try:
        if isinstance(name, unicode):
            name = name.encode("utf-8")
    except NameError:
        pass
    return socket.inet_ntoa(
        fcntl.ioctl(s.fileno(), 0x8915, struct.pack("256s", name))[20:24]
    )


def usb_audio_present():
    try:
        with open("/proc/asound/cards", "r") as f:
            data = f.read().lower()
        if "usb" in data and ("audio" in data or "usb-audio" in data):
            return True
        if "usb-audio" in data:
            return True
    except IOError:
        pass

    try:
        out = subprocess.check_output(["aplay", "-l"])
        try:
            out = out.decode("utf-8", "ignore")
        except Exception:
            pass
        out = out.lower()
        if "usb" in out and "card" in out:
            return True
    except Exception:
        pass

    return False


def get_eth_speed_short(ifname="eth0"):
    # English-only comments
    carrier_path = "/sys/class/net/{}/carrier".format(ifname)
    speed_path = "/sys/class/net/{}/speed".format(ifname)

    try:
        carrier = open(carrier_path, "r").read().strip()
        if carrier != "1":
            return "NO LINK"
    except Exception:
        return "NO LINK"

    try:
        speed = open(speed_path, "r").read().strip()
    except Exception:
        return "LINK"

    if speed == "-1":
        return "LINK"

    try:
        s = int(speed)
    except Exception:
        return "LINK"

    if s >= 2500:
        return "2.5G"
    if s >= 1000:
        return "1G"
    if s >= 100:
        return "100M"
    if s >= 10:
        return "10M"
    return "LINK"


def draw_eth_icon(draw, x, y):
    draw.rectangle((x, y + 3, x + 16, y + 14), outline=255, fill=0)
    for i in range(4):
        draw.line((x + 3 + i * 3, y + 5, x + 3 + i * 3, y + 9), fill=255)
    draw.line((x + 8, y + 14, x + 8, y + 17), fill=255)


def draw_usb_icon(draw, x, y):
    draw.line((x + 8, y + 14, x + 8, y + 5), fill=255)
    draw.line((x + 8, y + 5, x + 4, y + 7), fill=255)
    draw.line((x + 8, y + 5, x + 12, y + 7), fill=255)
    draw.line((x + 8, y + 5, x + 8, y + 2), fill=255)
    draw.polygon([(x + 8, y + 1), (x + 6, y + 3), (x + 10, y + 3)], outline=255, fill=0)
    draw.rectangle((x + 11, y + 7, x + 14, y + 10), outline=255, fill=0)
    draw.ellipse((x + 2, y + 6, x + 4, y + 8), outline=255, fill=255)


def draw_speed_icon(draw, x, y):
    # English-only comments
    draw.arc((x, y, x + 12, y + 12), 200, 340, fill=255)
    draw.line((x + 6, y + 6, x + 10, y + 4), fill=255)
    draw.ellipse((x + 5, y + 5, x + 7, y + 7), outline=255, fill=255)
    return 12


def load_font(path, size):
    try:
        return ImageFont.truetype(path, size)
    except Exception:
        return ImageFont.load_default()


def text_size(draw, text, font):
    try:
        return draw.textsize(text, font=font)
    except Exception:
        return (len(text) * 6, 10)


RST = "P9_12"
disp = Adafruit_SSD1306.SSD1306_128_64(rst=RST, i2c_address=0x3C)

disp.begin()
disp.clear()
disp.display()

W, H = disp.width, disp.height

EDGE_X = 12
usable_w = W - EDGE_X * 2

font_path = "Pokemon X and Y.ttf"
font_ip = load_font(font_path, 22)
font_small = load_font(font_path, 14)

image = Image.new("1", (W, H))
draw = ImageDraw.Draw(image)


def centered_x(text, font):
    w, _ = text_size(draw, text, font)
    return EDGE_X + max(0, (usable_w - w) // 2)


try:
    ip = get_ip_address("eth0")
except IOError:
    ip = "NO IP"

speed = get_eth_speed_short("eth0")
usb_ok = usb_audio_present()

draw.rectangle((0, 0, W, H), outline=0, fill=0)

draw_eth_icon(draw, 2, 0)

if usb_ok:
    draw_usb_icon(draw, W - 18, 0)

draw.text((centered_x(ip, font_ip), 18), ip, font=font_ip, fill=255)

gap = 4
icon_w = 12
speed_w, _ = text_size(draw, speed, font_small)
group_w = icon_w + gap + speed_w
group_x = EDGE_X + max(0, (usable_w - group_w) // 2)

draw_speed_icon(draw, group_x, 48)
draw.text((group_x + icon_w + gap, 48), speed, font=font_small, fill=255)

disp.image(image)
disp.display()

# Auto blank after 5 minutes
time.sleep(300)

disp.clear()
disp.display()

# OLED off command, script must run again to turn it on
try:
    disp.command(0xAE)
except Exception:
    pass
