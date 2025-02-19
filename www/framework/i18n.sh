#!/bin/bash
if ! test -d locale
then
    echo "run this script in the parent directory of your locale directory"
    exit 1
fi

languages=`ls locale`

echo "extract strings..."
tempfile=`mktemp /tmp/localize.XXXXXXX`
find . -name "*.tpl" -or -name "*.php" > $tempfile
xgettext --from-code=UTF-8 -f $tempfile -L PHP -o locale/en_US/LC_MESSAGES/messages.po

echo "update languages..."
for lang in $languages; do
    echo "update $lang..."
    if [[ ! -a locale/$lang/LC_MESSAGES/messages.po ]] ; then
        echo "copy template..."
        cp locale/en_US/LC_MESSAGES/messages.po locale/$lang/LC_MESSAGES/messages.po
    fi
    cp locale/$lang/LC_MESSAGES/messages.po locale/$lang/LC_MESSAGES/messages_old.po
    msgmerge locale/$lang/LC_MESSAGES/messages_old.po locale/en_US/LC_MESSAGES/messages.po -o locale/$lang/LC_MESSAGES/messages.po
    rm locale/$lang/LC_MESSAGES/messages_old.po

    msgfmt -o locale/$lang/LC_MESSAGES/messages.mo locale/$lang/LC_MESSAGES/messages.po
done

rm $tempfile
