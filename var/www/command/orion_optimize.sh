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


##################
# sound profiles #
##################

# default
if [ "$1" == "default" ]; then
echo -n ondemand > /sys/devices/system/cpu/cpu0/cpufreq/scaling_governor
/etc/init.d/rsyslog stop
echo 0 > /proc/sys/vm/swappiness
echo noop > /sys/block/mmcblk0/queue/scheduler
echo 20 > /proc/sys/vm/dirty_ratio
echo 20 > /proc/sys/vm/dirty_background_ratio # увеличили страничный кеш 
echo "flush DEFAULT sound profile"
fi


if [ "$1" == "Eco-Mode" ]; then
echo -n conservative > /sys/devices/system/cpu/cpu0/cpufreq/scaling_governor
/etc/init.d/rsyslog stop
echo 0 > /proc/sys/vm/swappiness
echo noop > /sys/block/mmcblk0/queue/scheduler
echo 20 > /proc/sys/vm/dirty_ratio
echo 20 > /proc/sys/vm/dirty_background_ratio # увеличили страничный кеш 
echo "flush Eco-Mod sound profile"
fi


# dev
if [ "$1" == "dev" ]; then
echo "flush DEV sound profile 'fake'"
fi


if [ "$1" == "" ]; then
echo "Orion Optimize Script v$ver" 
echo "Usage: $0 {default|beta1|mod1|mod2} {startup}"
exit 1
fi
