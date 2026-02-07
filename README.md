# SQpl
StereoQ Player is a lightweight music player project (work in progress). It is based on free, open-source software and uses an older but well-proven Volumio web interface. Some UI and system tweaks were inspired by tsunamp.com.
StereoQ Player is free to use and distributed free of charge.
It is built on open-source components. Please see the LICENSE file and third-party licenses for details.

Audio engine:
- Patched RT MPD (older, stable version that runs reliably) with some modules disabled for better performance and simplicity
- Additional system tweaks, updated Linux kernel, and updated ALSA libraries

Project website:
https://norrest.github.io/StereoQ/
The website will be updated with releases, documentation, and notes.

Source code (GitHub):
https://github.com/norrest/SQpl

Dowloads (sourceforge.net):
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

