#!/bin/bash

#file="bq.md"
file="forms.md"

if [ -e "$file" ]; then
    # Convert input file to UTF-8 using iconv
    from_encoding=$(file -b --mime-encoding "$file")
    iconv -f "$from_encoding" -t UTF-8 -c "$file" > "$file.utf8"

    # Replace em dashes, en dashes, left and right single quotes using sed
    sed -i '' -e "s/—/-/g" \
             -e "s/–/-/g" \
             -e "s/‘/'/g" \
             -e "s/’/'/g" "$file.utf8"

    # Remove any carriage return characters using tr
    tr -d '\r' < "$file.utf8" > "$file.nocr"

    # Show the changes made by sed using git diff
    git diff --no-index --color=always "$file" "$file.nocr" | grep -E '^\x1b\[3[12]m'

    # Convert output file back to original encoding using iconv
    iconv -f UTF-8 -t "$from_encoding" -c "$file.nocr" > "$file"

    # Remove temporary UTF-8 and nocr files
    rm "$file.utf8" "$file.nocr"
else
    echo "$file not found"
fi
