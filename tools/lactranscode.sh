#!/bin/sh

############# CONFIG 
# folder with .dv files 
INCOMING=/tmp/incoming

# here go the transcoded videos
DST=/tmp/lacvid

# create DBFILE with `program2ff.php` from lac2011 website-docroot
DBFILE=/tmp/lac2013_db.sh

# ffmpeg2theora binary
FFMPEG2THEORA=ffmpeg2theora

# on lac@linuxaudio.org
LACDOCROOT=/home/sites/lac.linuxaudio.org/2013/docroot
LACURL=http://lac.linuxaudio.org/2013/


# don't rsync and add links to website
TESTING=1
# don't actually run generate script to encode videos
DRYRUN=1

############# HERE BE DRAGONS
if ! test -f $DBFILE; then
	echo "please generate database-dump"
	echo "php program2ff.php > $DBFILE"
	exit
fi
. $DBFILE
mkdir -p $DST
ENCSCRIPT=$(mktemp /tmp/encscript-XXX.sh)
chmod +x $ENCSCRIPT

echo -e "#!/bin/sh\nSQL=""\n" >> $ENCSCRIPT

############# FUNCTIONS

# create transcode-job script
function queue_file {
 myid=$1
 INFILE=$2
 eval eval '$VAR'$myid
cat >> $ENCSCRIPT << EOF
echo 
echo "#### $OUTFILE"
if [ -f "$INCOMING/.${INFILE}.done"  -o -f $DST/$OUTFILE ]; then
  echo "... dst file exists. skipping."
else
	$FFMPEG2THEORA \
		-v 6 --optimize \
		--aspect 4:3 \
		$META \
	  --location "IEM, Graz, AT" \
	  --organization "Linux Audio Conference 2013" \
		--license "CC" \
		-o $DST/$OUTFILE \
		$INCOMING/$INFILE
	touch $INCOMING/.${INFILE}.done
  SQL="${SQL}"'update activity set url_stream="${LACURL}recordings/$OUTFILE" WHERE id=$ID; '
fi

EOF
}

# print available IDs to assign
function print_ids {
  i=0
	while [ $i -lt $LASTID ]; do
		i=$[ $i + 1 ]
		grep "^$i -" $INCOMING/.*.assigned &> /dev/null && continue
    eval eval '$VAR'$i
		printf "%2i" $i
		echo ": $OUTFILE"
	done
}

#############  MAIN

# list files in incoming/ - but not assigned/processed ( ./.$file.[assigned|done] )
# assign act-id (title, author etc)

for file in $INCOMING/*; do
	bn=`basename "$file"`
  
	# ignore already transcoded files
	if [ -f "$INCOMING/.${bn}.done"  -o -f "$INCOMING/.${bn}.assigned" ]; then
		echo "skipping $bn"
		continue
	fi
	echo
	echo "###############################################################"
	while true; do
		echo " ####  FILE $bn ####"
		print_ids
		echo -n "-?- Enter numeric-ID, 'p' to launch mplayer or 'x' to skip this file: "
		read mynum
		if [ "$mynum" == "p" ]; then
			mplayer $file
			continue;
		fi
		if [ "$mynum" == "x" ]; then
			break;
		fi

		META=""
		eval eval '$VAR'$mynum
		if [ -n "$META" ]; then
			echo $META ## DEBUG
      echo "$mynum - $ID" >> $INCOMING/.${bn}.assigned
			queue_file $mynum $bn
			break
		else 
			echo "invalid ID. please try again."
			echo
		fi
  done
done

exit

echo "########" >> $ENCSCRIPT
test -n "$NOSYNC" && echo -n "#" >> $ENCSCRIPT
echo "rsync -rPu $DST/ lac@linuxaudio.org:$LACDOCROOT/recordings/" >> $ENCSCRIPT
echo "########" >> $ENCSCRIPT
test -n "$NOSYNC" && echo -n "#" >> $ENCSCRIPT
echo "echo \"\$SQL\"  | ssh lac@linuxaudio.org sqlite3 $LACDOCROOT/tmp/lac2013.db" >> $ENCSCRIPT

if [ -n "$DRYRUN" ]; then
  cat $ENCSCRIPT ## DEBUG
else
  sh $ENCSCRIPT 
fi

rm $ENCSCRIPT
