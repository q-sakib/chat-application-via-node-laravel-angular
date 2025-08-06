const express = require("express");
const { createServer } = require("http");
const { Server } = require("socket.io");
const dotenv = require("dotenv");
const { subscribeToRedis } = require("./redis");

dotenv.config();

const app = express();
const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: "*", // Change to frontend origin in production
    methods: ["GET", "POST"],
  },
});

const connectedUsers = new Map(); // socketId -> userId

io.on("connection", (socket) => {
  console.log("Socket connected:", socket.id);

  socket.on("user_connected", (userId) => {
    connectedUsers.set(socket.id, userId);
    console.log(`User ${userId} connected`);
  });

  socket.on("disconnect", () => {
    const userId = connectedUsers.get(socket.id);
    connectedUsers.delete(socket.id);
    console.log(`User ${userId} disconnected`);
  });
});

// Listen to Redis events and broadcast via Socket.IO
// subscribeToRedis(io, connectedUsers);

const PORT = process.env.PORT || 4000;
server.listen(PORT, () => {
  console.log(`🚀 Socket.IO server running on port ${PORT}`);
});
