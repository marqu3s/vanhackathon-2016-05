FROM node:argon
# replace this with your application's default port
EXPOSE 8890

# Create app directory
RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

# Install app dependencies
COPY package.json /usr/src/app/
RUN npm install

# Bundle app source
COPY . /usr/src/app

CMD [ "npm", "start" ]


# TO BUILD AND RUN THE NODE CONTEINER
# docker build -t joaomarques/node-vanhackathon --no-cache=false .
# docker run -p 49160:8890 --name node -d joaomarques/node-vanhackathon
# docker logs node


# TO RUN THE REDIS CONTAINER
# docker run -p 49161:6379 --name redis -d redis
