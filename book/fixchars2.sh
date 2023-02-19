#!/bin/bash
# This version seems to work fine.

for file in *.md; do
  if [ -f "$file" ]; then
    echo "Processing file: $file"
    sed -i'.bak' $'s/\xE2\x80\x94/-/g; s/\xE2\x80\x93/-/g; s/\xE2\x80\x98/'\''/g; s/\xE2\x80\x99/'\''/g;' "$file"
    tr -d '\r' < "$file" > "$file.tmp" && mv "$file.tmp" "$file"
    rm "$file.bak"
  fi
done
