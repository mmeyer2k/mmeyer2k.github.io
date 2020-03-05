#
# docker build --tag mmeyer2k-github-io .
#
FROM bretfisher/jekyll-serve

RUN apk add --update --no-cache nodejs