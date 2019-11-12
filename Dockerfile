FROM php:7.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends apt-utils
RUN apt-get install wget -y
RUN apt-get install flac -y
RUN apt-get install lame -y
RUN apt-get install sox -y

# TODO: use composer to download dependencies
COPY vendor /usr/src/albumconverter/vendor
COPY album.sh /usr/src/albumconverter
COPY album.php /usr/src/albumconverter

RUN ln -s /usr/src/albumconverter/album.sh /usr/bin/album
RUN chmod +x /usr/bin/album

WORKDIR /usr/src/albumconverter

