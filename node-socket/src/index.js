const express = require("express");
const { createServer } = require("http");
const { Server } = require("socket.io");
const dotenv = require("dotenv");
// const { subscribeToRedis } = require("./redis"); // Not used for now

dotenv.config();

const app = express();
const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: "*", // 👈 Use your actual Angular frontend URL
    // origin: "http://localhost:4200", // 👈 Use your actual Angular frontend URL
    methods: ["GET", "POST"],
  },
});

// Maps socket.id → userId
const connectedUsers = new Map();
// Maps userId → socket.id
const userSockets = new Map();

io.on("connection", (socket) => {
  console.log("✅ Socket connected:", socket.id);

  // When a user connects, map userId ↔ socketId
  socket.on("user_connected", (userId) => {
    connectedUsers.set(socket.id, userId);
    userSockets.set(userId, socket.id);
    console.log(`📡 User ${userId} connected`);
  });

  // Handle incoming message
  socket.on("send-message", (data) => {
    const { senderId, receiverId, content } = data;

    console.log(`💬 Message from ${senderId} to ${receiverId}: ${content}`);

    const receiverSocketId = userSockets.get(receiverId);
    if (receiverSocketId) {
      io.to(receiverSocketId).emit("receive-message", {
        senderId,
        content,
        timestamp: new Date().toISOString(),
      });
    }
  });

  socket.on("disconnect", () => {
    const userId = connectedUsers.get(socket.id);
    connectedUsers.delete(socket.id);
    userSockets.delete(userId);
    console.log(`❌ User ${userId} disconnected`);
  });
});

// Not using Redis for now
// subscribeToRedis(io, connectedUsers);

const PORT = process.env.PORT || 4000;
server.listen(PORT, () => {
  console.log(`🚀 Socket.IO server running on http://localhost:${PORT}`);
});
