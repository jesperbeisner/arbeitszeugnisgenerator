# Arbeitszeugnisgenerator 📑

My buddy is too lazy to copy simple text blocks by hand, here is the solution! 🎉

## Deployment

### 1. Remove your running container

```bash
docker rm -f arbeitszeugnisgenerator-prod
```

### 2. Remove the old image

```bash
docker rmi -f ghcr.io/jesperbeisner/arbeitszeugnisgenerator:latest
```

### 3. Start a new container with the new image

```bash
docker run -d -p 7777:80 --name arbeitszeugnisgenerator-prod ghcr.io/jesperbeisner/arbeitszeugnisgenerator:latest
```

### 4. Finished

Deployment is done, and you are now running the newest version. 🚀
