FROM node:4-onbuild
# replace this with your application's default port
EXPOSE 8890

# Create app directory
RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

# Install app dependencies
COPY package.json /usr/src/app/
RUN npm install

CMD [ "npm", "start" ]
