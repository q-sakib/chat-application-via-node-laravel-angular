const { createClient } = require("redis");

function subscribeToRedis(io, users) {
  const redis = createClient({ url: "redis://localhost:6404" });

  redis
    .connect()
    .then(() => {
      console.log("🔌 Connected to Redis");

      redis.subscribe("chat-channel", (message) => {
        const data = JSON.parse(message);

        console.log("📨 New message from Redis:", data);

        // Emit to specific users (find sockets for receiver_id)
        for (const [socketId, userId] of users.entries()) {
          if (parseInt(userId, 10) === data.receiver_id) {
            io.to(socketId).emit("new_message", data);
          }
        }
      });
    })
    .catch(console.error);
}

module.exports = {
  subscribeToRedis,
};
