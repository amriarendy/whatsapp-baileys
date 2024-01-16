const {
  default: makeWASocket,
  useMultiFileAuthState,
} = require("@whiskeysockets/baileys");

const pino = require("pino");

async function bot() {
  const auth = await useMultiFileAuthState("sessions");
  const socket = makeWASocket({
    printQRInTerminal: true,
    browser: ["Tutorialn Bot Baileys", "Chrome", "1.0.0"],
    auth: auth.state,
    logger: pino({ level: "silent" }),
  });

  socket.ev.on("creds.update", auth.saveCreds);
  socket.ev.on("connection.update", ({ connection }) => {
    if (connection === "open") {
      console.log("App running..");
    }
    if (connection === "close") {
      bot();
    }
  });

  socket.ev.on("messages.upsert", ({ messages }) => {
    const msg = messages[0];
    const keyword = msg.message.conversation;
    function reply(text) {
      socket.sendMessage(
        msg.key.remoteJid,
        {
          text: text,
        },
        {
          quoted: msg,
        }
      );
    }
    if (keyword === "ping") {
      reply("pong!!");
      return;
    }
    if (keyword === "bot") {
      reply("Project actived!");
      return;
    }

    switch (keyword) {
      case "menu": {
        reply("Menu disini.");
        break;
      }
      case "owner": {
        reply("amriarendy");
        break;
      }
      default:
    }
  });
}
bot();
