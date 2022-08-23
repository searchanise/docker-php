
**Usage:**
```bash
# From root project folder
docker build -t searchanise/php:7.3-fpm -f ./docker/php/7.3/Dockerfile ./docker/php/
docker build -t searchanise/php:7.4-fpm -f ./docker/php/7.4/Dockerfile ./docker/php/
docker build -t searchanise/php:8.0-fpm -f ./docker/php/8.0/Dockerfile ./docker/php/
docker build -t searchanise/php:8.1-fpm -f ./docker/php/8.1/Dockerfile ./docker/php/

# Login to hub.docker.com
echo $DOCKER_HUB_TOKEN | docker login -u USERNAME --password-stdin

# Push image to github docker registry
docker push searchanise/php:7.3-fpm
docker push searchanise/php:7.4-fpm
docker push searchanise/php:8.0-fpm
docker push searchanise/php:8.1-fpm
```

List of available Docker PHP extensions:
https://gist.github.com/hoandang/88bfb1e30805df6d1539640fc1719d12