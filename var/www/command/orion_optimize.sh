#!/bin/bash

ver="0.9"

if [ "$2" = "startup" ]; then
killall -q exim4 2>/dev/null || true
killall -q ntpd 2>/dev/null || true
killall -q thd 2>/dev/null || true
killall -q cron 2>/dev/null || true
killall -q atd 2>/dev/null || true
sh /home/volumio/unmute.sh >/dev/null 2>&1 || true
echo "flush startup settings"
fi

set_governor() {
for cpu in /sys/devices/system/cpu/cpu[0-9]*; do
  [ -w "$cpu/cpufreq/scaling_governor" ] || continue
  echo -n "$1" > "$cpu/cpufreq/scaling_governor" 2>/dev/null || true
done
}

##################
# sound profiles #
##################

if [ "$1" = "default" ]; then
set_governor ondemand
echo 0 > /proc/sys/vm/swappiness
echo noop > /sys/block/mmcblk0/queue/scheduler
echo 20 > /proc/sys/vm/dirty_ratio
echo 20 > /proc/sys/vm/dirty_background_ratio
echo "flush DEFAULT sound profile"
fi

if [ "$1" = "Eco-Mode" ]; then
set_governor conservative
echo 0 > /proc/sys/vm/swappiness
echo noop > /sys/block/mmcblk0/queue/scheduler
echo 20 > /proc/sys/vm/dirty_ratio
echo 20 > /proc/sys/vm/dirty_background_ratio
echo "flush Eco-Mode sound profile"
fi

if [ "$1" = "dev" ]; then
echo "flush DEV sound profile 'fake'"
fi

if [ -z "$1" ]; then
echo "Orion Optimize Script v$ver"
echo "Usage: $0 {default|Eco-Mode|dev} {startup}"
exit 1
fi
