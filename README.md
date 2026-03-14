# SQpl
StereoQ Player is a lightweight music player project (work in progress). It is based on free, open-source software and uses an older but well-proven Volumio web interface. Some UI and system tweaks were inspired by tsunamp.com.
StereoQ Player is free to use and distributed free of charge.
It is built on open-source components. Please see the LICENSE file and third-party licenses for details.

Project website:
https://norrest.github.io/StereoQ/
The website will be updated with releases, documentation, and notes.

--------------------
3.04 UI (14 Mar 2026)

- Added a new Service Menu entry for updating Shairport Sync used by AirPlay
- Added support for updating Shairport Sync from version 2.4 to version 3.3.9
- Fixed reboot and power-off from the Web UI so they now work correctly  when AirPlay is enabled
- Fixed the Service Menu popup styling to better match the updated StereoQ Player UI
- Removed the unnecessary scrollbar from the Service Menu popup
- Fixed the Browse page JavaScript error "showtype is not defined"
- Adjusted the default CPU governor from conservative to ondemand for better system responsiveness
- Cleaned up the legacy startup optimization script by removing unnecessary forced network MTU reset and other obsolete tweaks
- Added a Web UI version status hint to StereoQ Player Info page

Note:
- To get the new AirPlay Update entry in the Service Menu, first update the Web UI to version 3.04

After this update, StereoQ Player Info will show the current Web UI version together with a status message:
- You have the latest Web UI version
- A newer Web UI version is available. Update it via Service Menu → Update Web Interface
- Cannot check for updates

--------------------
3.03 UI (03 Mar 2026)

- Fixed boot-time log noise by disabling login accounting logs (wtmp, btmp, lastlog) via tmpfiles to reduce SD-card writes
- Removed legacy ethtool -s eth0 speed 100 duplex half line from /etc/rc.local and ensured chmod -R 777 /mnt/* is present without duplicates
- Removed ntpd package and left one-shot time sync via ntpdate to avoid UDP/123 conflicts
- Disabled Samba AD DC and NetBIOS services (samba-ad-dc, nmbd) to speed up boot while keeping SMB access via \\IP\share
- Optimized Samba for IP-only access (bind to eth0, port 445 only, NetBIOS disabled)
- Speeded up smbd startup by removing the slow samba-tool check in the init script
- Updated /etc/rc.local: run shapes_and_ip_clone.py in background on Banana Pi boards with OLED display to show quick IP/status info, without delaying boot
- Disabled hostapd on devices without Wi-Fi and removed unused bonding autoload entry
- Built a separate Cubietruck firmware with SPDIF support
-- Disabled Bluetooth modules to stop BCM reset/timeout spam in kernel logs
-- Switched default target to multi-user.target and masked display-manager.service to avoid unnecessary graphical target warnings

--------------------
3.02 UI (14 Feb 2026)

- Updated Samba (SMB) settings to improve compatibility with Windows and other operating systems.
- Added direct network paths to the Music and WebRadio folders in the Settings menu for easier access and file transfer.
- Added time synchronization before updates, including automatic installation of the ntpdate package when missing, and saving the correct time to the hardware clock to prevent HTTPS and Git certificate errors.
<img width="700" height="302" alt="image" src="https://github.com/user-attachments/assets/b9e58666-5865-4e7b-afd7-9ee658ecc956" />

--------------------
Latest build for  Banana Pi M1 and Cubieboard A10:

- Player UI: updated and refreshed, with several minor UI bugs fixed. Full rebranding from V.A.M.P. to StereoQ Player was completed. For network compatibility and existing setups, the device hostname was intentionally kept as Volumio.

- AirPlay (shairport-sync): fixed the missing shairport-sync service required for AirPlay support. After this fix, the player should be discoverable in some networks as volumio.lan, depending on local DNS and mDNS behavior.

- MPD: upgraded to stable MPD 0.20.23 and patched with RT scheduling improvements to make playback controls and track transitions more responsive and less prone to timing issues under load.

- Linux kernel: upgraded to Linux 6.6.75, built using the Armbian build script and extended with additional patches to keep the kernel cleaner and reduce unnecessary components.

- ALSA library: upgraded to ALSA 1.2.15.3. This is required to stay compatible with the newer kernel audio stack and to improve stability and device compatibility for modern USB audio interfaces, reducing the chance of glitches, quirks, or unexpected behavior with some USB DACs.

- USB autosuspend: disabled globally. This change prevents USB devices, including any future USB DACs, from entering power-saving states that can cause wake-up glitches, random disconnects, or audible clicks.

- USB audio power management: forced to stay ON (power/control=on) for USB Audio interfaces. This keeps runtime power saving from toggling the USB DAC interface and helps avoid dropouts and pops caused by power state transitions.

- MPD scheduling priority: increased (Nice=-10, tuned I/O scheduling). This gives MPD higher priority during playback and track changes, reducing the chance of stutter or timing glitches under load.

- USB DAC lowlatency mode: disabled in the snd-usb-audio driver. This favors stability and compatibility over the most aggressive latency settings, which can be problematic on some USB audio devices and controllers.

- USB DAC Resync (formerly “CMedia fix”): implemented as a track-change only resync. When enabled, the player performs a quick resync action only when the track actually changes, targeting click/pop issues without constantly interfering during normal playback.

- USB implicit feedback (implicit_fb): added as a configurable option in the UI and stored persistently. This enables the driver’s implicit feedback mode for specific asynchronous USB DACs that can suffer from sync drift or periodic glitches without it.

- USB auto clock (autoclock): added as a configurable option in the UI and stored persistently. This controls automatic clock selection for UAC2 devices and is intended to improve stability with DACs that behave differently depending on clock selection behavior.
  
- Audio engine:
Patched RT MPD (older, stable version that runs reliably) with some modules disabled for better performance and simplicity
Additional system tweaks, updated Linux kernel, and updated ALSA libraries

--------------------
<img width="700" alt="image" src="https://github.com/user-attachments/assets/df766192-6bb3-4599-92e3-79e29f2af3b2" />
<img width="700" alt="image" src="https://github.com/user-attachments/assets/ff5b55e3-453a-4d14-af29-2cc61c8837cf" />
<img width="700" alt="image" src="https://github.com/user-attachments/assets/839ce5c1-3f35-4918-899b-42a0ee5e1f53" />
<img width="700" alt="image" src="https://github.com/user-attachments/assets/3d4440ed-98fb-48ae-bc4f-562bcbb2cb51" />
<img width="700" alt="image" src="https://github.com/user-attachments/assets/07968ff6-4779-4940-8b68-db03c75266bc" />

## Download IMG firmware files

Prebuilt StereoQ Player IMG firmware files are available on SourceForge for the following boards:

- Cubietruck
- Cubieboard A20
- Cubieboard A10
- Banana Pi M1

Download page:
https://sourceforge.net/projects/strereoq/

Storage:
StereoQ Player supports internal SATA drives. Format the drive as EXT4 and it will be mounted automatically at /mnt/USB.
The folder name is historical, please ignore it.
The mounted drive is also shared on the network as “volumio”, out of respect for the original authors and compatibility.

Network (Samba):
Please note that Samba access uses the root / root credentials by default.
You can change them via the console if you want.

Note:
This project does not use the newer Volumio 2 code or configuration. Volumio 2 unfortunately became a paid product.

SSH access:
Default credentials are root / rootfs.
For security, change the default password after the first login.

--------------------

Music (DJ) is my hobby:
https://www.youtube.com/@StereoQ-MUSIC

I would really appreciate your like and subscription.

