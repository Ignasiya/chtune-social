FROM node:22.1 as npm

ARG UID=10001
ARG GID=10001

RUN groupmod --gid=${GID} node \
    && usermod --uid=${UID} --gid=${GID} node

USER node
