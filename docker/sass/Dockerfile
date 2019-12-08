FROM node:11.15.0-alpine

ARG SERVICE_DIR="./sass"
ARG WORK_DIR=/etc/site-sass

COPY ./shared/scripts/ /tmp/scripts/
RUN chmod -R 777 /tmp/scripts/

# set timezone
ARG TZ=UTC
RUN /tmp/scripts/set_timezone.sh ${TZ}

WORKDIR ${WORK_DIR}

COPY ${SERVICE_DIR}/gulpfile.js ${WORK_DIR}
COPY ${SERVICE_DIR}/package.json ${WORK_DIR}

RUN mkdir -p ${WORK_DIR}/sass && mkdir -p ${WORK_DIR}/css

RUN \
  apk add --no-cache dumb-init && \
  npm update && \
  npm install --global gulp-cli && \
  npm install && \
  chown -R node:node ${WORK_DIR}

VOLUME ${WORK_DIR}

ENV SASS_FILES="**/*.+(sass|scss)"

# cleanup
RUN /tmp/scripts/cleanup.sh

USER node

ENTRYPOINT [ "/usr/bin/dumb-init", "--" ]
CMD [ "gulp" ]