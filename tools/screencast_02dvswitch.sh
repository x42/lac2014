#!/bin/sh

export DISPLAY=:2.0

# start audio-stream
#xterm -display :0.0 -e dvswitch-jack &
# TODO: auto-connect JACK ports...

# start screencast-capture
ffmpeg -f x11grab -r 25 -s 720x576 -i :2 \
	-ar 48000 -ac 2 -f s16le -acodec pcm_s16le -i /dev/zero \
	-map 0.0 -map 1.0 \
	-aspect 4:3 -f dv -vcodec dvvideo -threads 2 \
  - | dvsource-file /dev/stdin

