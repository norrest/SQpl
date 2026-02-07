# SQpl
StereoQ Player is a lightweight music player project (work in progress). It is based on free, open-source software and uses an older but well-proven Volumio web interface. Some UI and system tweaks were inspired by tsunamp.com.
StereoQ Player is free to use and distributed free of charge.
It is built on open-source components. Please see the LICENSE file and third-party licenses for details.

--------------------
Latest build for Banana Pi M1 : 

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


--------------------

Audio engine:
Patched RT MPD (older, stable version that runs reliably) with some modules disabled for better performance and simplicity
Additional system tweaks, updated Linux kernel, and updated ALSA libraries

Project website:
https://norrest.github.io/StereoQ/
The website will be updated with releases, documentation, and notes.

Source code (GitHub):
https://github.com/norrest/SQpl

Dowloads (sourceforge.net):
https://sourceforge.net/projects/strereoq/

Music (Dj):
https://www.youtube.com/@StereoQ-MUSIC

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

