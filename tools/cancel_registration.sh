#!/bin/sh

## crude registration cancellation script
## for now, we simply add a new field
##   'cancelled="1"
## to the .ini file (given at the cmdline)
## and don't worry about other databases

## note: don't remove the reg-files, as they
##       are needed to keep the user/passwd mapping
##       (think wifi) consistent

usage() {
  echo "$0 <regfile.ini>" 1>&2
  echo "	will cancel the registration of the attendee in the given regfile" 1>&2
  exit 1
}

for REGFILE in $@; do
  if [ -e "${REGFILE}" ]; then
   sed -e 's|^reg_cancelled=.*$|reg_cancelled="1"|' -i "${REGFILE}"
   egrep "^reg_cancelled=" "${REGFILE}" > /dev/null 2>&1 || \
   echo 'reg_cancelled="1"' >> "${REGFILE}"
  
   RESULT=$(egrep "^reg_cancelled=" "${REGFILE}")
   echo "${REGFILE}: ${RESULT}"
  else
   echo "couldn't find ${REGFILE}"
   usage
  fi
done
