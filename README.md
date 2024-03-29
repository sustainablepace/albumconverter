Converts a set of FLAC files into a single seamless mp3 file with tags from https://www.allmusic.com.

# Using Docker

## Start Docker

```
colima start --mount ~/Music/Convert/:w --verbose
```

## Build the image and start a container

and mount a host folder with FLAC files

```
docker build -t <image> .
docker run -itd -v <host folder with flac>:/flac --name <container> <image>
```

for example

```
docker build -t album .
docker run -itd -v ~/Music/Convert:/flac --name album album
```

## Work on the FLAC files

```
docker exec -it <container> /bin/bash

cd /flac

// convert all files
album 

// or convert first n files (to exclude bonus tracks)
album n
```
An mp3 is created and saved in the FLAC folder.

# TO DO

Disclaimer: This is work in progress and will most likely never happen.

## Incident
- Invalid characters in filename (like ?)
- Deal with missing cover art in allmusic

## Improvement
- Smart guess the album id at allmusic from folder name
- User needs to confirm if crawled data is valid
