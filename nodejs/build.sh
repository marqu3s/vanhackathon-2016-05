docker stop node
docker rm node
docker build -t joaomarques/node-vanhackathon --no-cache=false .
docker run -p 49160:8890 --name node -d joaomarques/node-vanhackathon
docker logs node
