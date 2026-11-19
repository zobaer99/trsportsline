// Passenger/cPanel entry point for the production Next.js server.
process.env.NODE_ENV = process.env.NODE_ENV || "production";

const { createServer } = require("node:http");
const next = require("next");

const hostname = "0.0.0.0";
const port = Number.parseInt(process.env.PORT || "3000", 10);
const app = next({ dev: false, hostname, port });
const handle = app.getRequestHandler();

app.prepare()
  .then(() => {
    createServer((request, response) => handle(request, response))
      .listen(port, hostname, () => {
        console.log(`TR Sports Line ready on ${hostname}:${port}`);
      });
  })
  .catch((error) => {
    console.error("Unable to start TR Sports Line", error);
    process.exit(1);
  });
