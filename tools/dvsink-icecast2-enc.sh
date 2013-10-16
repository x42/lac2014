#!/bin/sh

FFMPEG2THEORA=ffmpeg2theora

#NOTE: most players won't ever show the meta-data
# VLC can catch and display meta-data updates while the 
# stream is continuing (mplayer can't)
#
# BTW: only title and artist are shown in live-streams
# date,location, etc are basically only required for archiving
#

exec $FFMPEG2THEORA \
  -v 4 --speedlevel 2 \
  --aspect 4:3 \
	--title "LAC 2012" \
	--location "Campbell Recital Hall, Stanford, CA, US" \
	--organization "Linux Audio Conference 2012" \
	--license "CC" \
	-o - -

exit

# example for archiving -> use `lactranscode.sh`
exec $FFMPEG2THEORA \
  -v 4 --speedlevel 2 \
  --aspect 4:3 \
	--title "Keynote" \
	--artist "Dave Phillips" \
	--date "2012-04-12 11:00:00" \
	--location "Campbell Recital Hall, Stanford, CA, US" \
	--organization "Linux Audio Conference 2012" \
	--license "CC" \
	-o - -
