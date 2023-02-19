#!/bin/bash
# This version seems to work fine.

total_replacements=0

for file in *.md; do
    num_replacements=$(sed -i '' -e $'s/\xE2\x80\x94/-/g' \
                                 -e $'s/\xE2\x80\x93/-/g' \
                                 -e $'s/\xE2\x80\x98/\x27/g' \
                                 -e $'s/\xE2\x80\x99/\x27/g' "$file" | wc -l)
    total_replacements=$((total_replacements + num_replacements))
    echo "Replaced $num_replacements occurrences in $file"
done

echo "Total replacements: $total_replacements"
