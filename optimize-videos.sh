#!/usr/bin/env bash
# Optimize videos: remove audio, re-encode with libx264 for smaller size
# Source: videos-original/  →  Output: videos/

set -e
mkdir -p videos

for f in videos-original/*.mp4; do
  base=$(basename "$f")
  echo "Processing: $base"
  ffmpeg -y -i "$f" -an -c:v libx264 -crf 23 -preset medium -tune animation "videos/$base"
done

echo "Done. Optimized videos are in videos/"
