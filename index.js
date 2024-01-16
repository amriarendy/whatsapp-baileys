const {
  default: makeWASocket,
  MessageType,
  MessageOptions,
  Mimetype,
  DisconnectReason,
  BufferJSON,
  AnyMessageContent,
  delay,
  fetchLatestBaileysVersion,
  isJidBroadcast,
  makeCacheableSignalKeyStore,
  makeInMemoryStore,
  MessageRetryMap,
  useMultiFileAuthState,
  msgRetryCounterMap,
} = require("@whiskeysockets/baileys");

const log = (pino = require("pino"));
const { session } = { session: "auth_session_info" };
const { Boom } = require("@hapi/boom");
const path = require("path");
const fs = require("fs");
const http = require("http");
const https = require("https");
const express = require("express");
const fileUpload = require("express-fileupload");
const cors = require("cors");
const bodyParser = require("body-parser");
const db = require("./helpers/database");
const app = require("express")();
const {
  phoneNumberFormatter,
  groupIdFormatter,
  numberFormatter,
} = require("./helpers/formatter");
// enable files upload
app.use(
  fileUpload({
    createParentPath: true,
  })
);

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
const server = require("http").createServer(app);
const io = require("socket.io")(server);
const port = process.env.PORT || 3000;
const qrcode = require("qrcode");
const { body, query, check, validationResult } = require("express-validator");

app.get("/", (req, res) => {
  res.redirect("http://127.0.0.1/");
});

// function voice capital
function capital(textSound) {
  const arr = textSound.split(" ");
  for (var i = 0; i < arr.length; i++) {
    arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
  }
  const str = arr.join(" ");
  return str;
}
const store = makeInMemoryStore({
  logger: pino().child({ level: "silent", stream: "store" }),
});

let sock;
let qr;
let soket;

async function connectToWhatsApp() {
  const { state, saveCreds } = await useMultiFileAuthState("auth_session_info");
  let { version, isLatest } = await fetchLatestBaileysVersion();
  sock = makeWASocket({
    printQRInTerminal: true,
    browser: ["Whatsapp Bot", "Chrome", "1.0.0"],
    auth: state,
    logger: log({ level: "silent" }),
    version,
    shouldIgnoreJid: (jid) => isJidBroadcast(jid),
  });
  store.bind(sock.ev);
  sock.multi = true;
  sock.ev.on("connection.update", async (update) => {
    //console.log(update);
    const { connection, lastDisconnect } = update;
    if (connection === "close") {
      let reason = new Boom(lastDisconnect.error).output.statusCode;
      if (reason === DisconnectReason.badSession) {
        console.log(
          `Bad Session File, Please Delete ${session} and Scan Again`
        );
        sock.logout();
      } else if (reason === DisconnectReason.connectionClosed) {
        console.log("Connection closed, reconnecting....");
        connectToWhatsApp();
      } else if (reason === DisconnectReason.connectionLost) {
        console.log("Connection lost from server, reconnecting...");
        connectToWhatsApp();
      } else if (reason === DisconnectReason.connectionReplaced) {
        console.log(
          "Connection replaced, another new session opened, please close current session first"
        );
        sock.logout();
      } else if (reason === DisconnectReason.loggedOut) {
        console.log(
          `Device logged out, please delete ${session} and Scan Again.`
        );
        fs.readdir(`auth_session_info`, (err, files) => {
          if (err) throw err;
          for (const file of files) {
            fs.unlink(path.join("auth_session_info", file), (err) => {
              if (err) throw err;
            });
          }
        });
        connectToWhatsApp();
      } else if (reason === DisconnectReason.restartRequired) {
        console.log("Restart required, restarting...");
        connectToWhatsApp();
      } else if (reason === DisconnectReason.timedOut) {
        console.log("Connection timedOut, reconnecting...");
        connectToWhatsApp();
      } else {
        sock.end(`Unknown DisconnectReason: ${reason}|${lastDisconnect.error}`);
      }
    } else if (connection === "open") {
      console.log("Connection Ready..");
      // let groups = Object.values(await sock.groupFetchAllParticipating());
      // for (let group of groups) {
      //   if (group.id !== (await db.duplicateGroup(group.id))) {
      //     await db.insertGroup(group.id, group.subject);
      //     // console.log('Success create data')
      //   }
      // }
    }
    if (update.qr) {
      qr = update.qr;
      updateQR("qr");
    } else if ((qr = undefined)) {
      updateQR("loading");
    } else {
      if (update.connection === "open") {
        updateQR("qrscanned");
        return;
      }
    }
  });
  sock.ev.on("contacts.upsert", async (contacts) => {
    console.log(contacts);
  });
  sock.ev.on("creds.update", saveCreds);
  sock.ev.on("messages.upsert", async ({ messages, type }) => {
    //console.log(messages);
    if (type === "notify") {
      if (!messages[0].key.fromMe) {
        const captureMessage = messages[0].message.conversation;
        const numberWA = messages[0].key.remoteJid;
        // await sock.readMessages([messages[0].key]);
        const keyword = captureMessage.toLowerCase();
        const replyMessage = await db.getAutoreply(keyword);
        if (!messages[0].key.fromMe && replyMessage !== false) {
          if (replyMessage.param === "image") {
            // image message
            await sock.sendMessage(
              numberWA,
              {
                image: {
                  url: replyMessage.autoreply.media,
                },
                caption: replyMessage.autoreply.response,
              },
              { quoted: messages[0] }
            );
          } else if (replyMessage.param === "audio") {
            // audio message
            await sock.sendMessage(
              numberWA,
              {
                audio: {
                  url: replyMessage.autoreply.media,
                  caption: replyMessage.autoreply.response,
                },
                mimetype: "audio/mp4",
              },
              { quoted: messages[0] }
            );
          } else if (replyMessage.param === "document") {
            // document message
            await sock.sendMessage(
              numberWA,
              {
                document: {
                  url: replyMessage.autoreply.media,
                },
                caption: replyMessage.autoreply.response,
              },
              { quoted: messages[0] }
            );
          } else {
            // text message
            await sock.sendMessage(
              numberWA,
              {
                text: replyMessage.autoreply.response,
              },
              { quoted: messages[0] }
            );
          }
        }
      }
    }
  });
}

io.on("connection", async (socket) => {
  soket = socket;
  // console.log(sock)
  if (isConnected) {
    updateQR("connected");
  } else if (qr) {
    updateQR("qr");
  }
});

const isConnected = () => {
  return sock.user;
};

const updateQR = (data) => {
  switch (data) {
    case "qr":
      qrcode.toDataURL(qr, (err, url) => {
        soket?.emit("qr", url);
        soket?.emit("log", "QR Code received, scan please!");
      });
      break;
    case "connected":
      soket?.emit("qrstatus", ".././public/files/check.svg");
      soket?.emit("log", "Connecting Server!");
      soket?.emit("logout");
      break;
    case "qrscanned":
      soket?.emit("qrstatus", ".././public/files/check.svg");
      soket?.emit("log", "Success scan QR Code!");
      break;
    case "loading":
      soket?.emit("qrstatus", ".././public/files/loading.gif");
      soket?.emit("log", "Registering QR Code , please wait!");
      break;
    default:
      break;
  }
};

app.post("/logout-session", async (req, res) => {
  const token = req.body.token;
  const tokenKey = await db.getToken(token);
  try {
    if (tokenKey === token) {
      if (isConnected()) {
        // Perform the logout here
        sock
          .logout()
          .then(() => {
            console.log("Logout successful");
            res.status(200).json({ status: 200, message: "Success" });
          })
          .catch((error) => {
            console.error("Error during logout:", error);
            res.status(500).json({
              status: 500,
              message: "Internal server error",
              errors: error.message,
            });
          });
      } else {
        res
          .status(401)
          .json({ status: 401, message: "Unauthorized: Not connected" });
      }
    } else {
      res.status(401).json({
        status: 401,
        response: "Unauthorized: Invalid key",
      });
    }
  } catch (err) {
    res.status(500).send(err);
  }
});

app.get(
  "/check-numbers",
  [
    check("number").notEmpty(),
    check("api_key").notEmpty(),
    check("token").notEmpty(),
  ],
  async (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({
        status: 400,
        message: "Bad request!",
        errors: errors.array(),
      });
    }
    const number = req.query.number;
    const api_key = req.query.api_key;
    const token = req.query.token;
    try {
      const numberWA = numberFormatter(number);
      const tokenKey = await db.getToken(token);
      const apiKey = await db.getKey(api_key);
      const [result] = await sock.onWhatsApp(numberWA);
      if (tokenKey === token && apiKey === api_key) {
        if (isConnected()) {
          if (result && result.exists) {
            let data = [];
            const profile = await sock.getBusinessProfile(numberWA);
            data.push({
              number: result.jid,
              description: profile,
            });
            res.status(200).json({
              status: 200,
              message: "Success",
              data: data,
            });
          } else {
            res.status(401).json({
              status: 401,
              message: `Unauthorized: Whatsapp number not exists`,
            });
          }
        } else {
          res.status(401).json({
            status: 401,
            message: "Unauthorized: Not connected",
          });
        }
      } else {
        res.status(401).json({
          status: 401,
          message: `Unauthorized: Invalid key`,
        });
      }
    } catch (error) {
      // console.log(error);
      res.status(500).json({
        status: 500,
        message: "Internal server error",
        errors: error.message,
      });
    }
  }
);

app.get(
  "/groups",
  [check("api_key").notEmpty(), check("token").notEmpty()],
  async (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({
        status: 400,
        message: "Bad request!",
        errors: errors.array(),
      });
    }
    const token = req.query.token;
    const api_key = req.query.api_key;
    try {
      const tokenKey = await db.getToken(token);
      const apiKey = await db.getApi(api_key);
      if (tokenKey === token && apiKey.api_key === api_key) {
        if (isConnected()) {
          let groups = Object.values(await sock.groupFetchAllParticipating());
          let data = [];
          for (let group of groups) {
            data.push({
              id_group: group.id,
              name: group.subject,
              subjectOwner: group.subjectOwner,
              owner: group.owner,
              desc: group.desc,
            });
          }
          res.status(200).json({
            status: 200,
            message: "Success fetch group participating",
            data: data,
          });
        } else {
          res.status(401).json({
            status: 401,
            message: "Unauthorized: Not connected",
          });
        }
      } else {
        res.status(401).json({
          status: 401,
          message: `Unauthorized: Invalid key`,
        });
      }
    } catch (error) {
      res.status(500).json({
        status: 500,
        message: "Internal Server Error",
        errors: error.message,
      });
      console.log(error);
    }
  }
);

// send text message to wa user
app.post(
  "/send-message",
  [
    check("number").notEmpty(),
    check("message").notEmpty(),
    check("token").notEmpty(),
  ],
  async (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({
        status: 400,
        message: "Bad request!",
        errors: errors.array(),
      });
    }
    const sendMessage = req.body.message;
    const number = req.body.number;
    const token = req.body.token;
    const tokenKey = await db.getToken(token);
    let numberWA;
    try {
      if (tokenKey === token) {
        if (!req.body.file) {
          if (!number) {
            res.status(400).json({
              status: 400,
              message: "Bad request!",
            });
          } else {
            // numberWA = "62" + number.substring(1) + "@s.whatsapp.net";
            numberWA = phoneNumberFormatter(number);
            // console.log(await sock.onWhatsApp(numberWA));
            if (isConnected) {
              const exists = await sock.onWhatsApp(numberWA);
              if (exists?.jid || (exists && exists[0]?.jid)) {
                sock
                  .sendMessage(exists.jid || exists[0].jid, {
                    text: sendMessage,
                  })
                  .then((result) => {
                    res.status(200).json({
                      status: 200,
                      message: "Success",
                    });
                  })
                  .catch((error) => {
                    res.status(500).json({
                      status: 500,
                      message: "Internal server error",
                      errors: error.message,
                    });
                  });
              } else {
                res.status(401).json({
                  status: 401,
                  message: `Unauthorized: Whatsapp ${number} not exists`,
                });
              }
            } else {
              res.status(401).json({
                status: 401,
                message: "Unauthorized: Not connected",
              });
            }
          }
        } else {
          //console.log('send document');
          if (!number) {
            res.status(400).json({
              status: 400,
              message: "Bad request!",
            });
          } else {
            numberWA = phoneNumberFormatter(number);
            let saveFile = req.body.file;

            if (isConnected) {
              const exists = await sock.onWhatsApp(numberWA);

              if (exists?.jid || (exists && exists[0]?.jid)) {
                let nameFileSent = saveFile;
                let extensionName = path.extname(nameFileSent);
                if (
                  extensionName === ".jpeg" ||
                  extensionName === ".jpg" ||
                  extensionName === ".png" ||
                  extensionName === ".gif"
                ) {
                  await sock
                    .sendMessage(exists.jid || exists[0].jid, {
                      image: {
                        url: nameFileSent,
                      },
                      caption: sendMessage,
                    })
                    .then((result) => {
                      res.send({
                        status: 200,
                        message: "Success",
                      });
                    })
                    .catch((error) => {
                      res.status(500).json({
                        status: 500,
                        message: "Internal server error",
                        errors: error.message,
                      });
                    });
                } else if (
                  extensionName === ".mp3" ||
                  extensionName === ".ogg"
                ) {
                  await sock
                    .sendMessage(exists.jid || exists[0].jid, {
                      audio: {
                        url: nameFileSent,
                        caption: sendMessage,
                      },
                      mimetype: "audio/mp4",
                    })
                    .then((result) => {
                      res.send({
                        status: 200,
                        message: "Success",
                      });
                    })
                    .catch((error) => {
                      res.status(500).json({
                        status: 500,
                        message: "Internal server error",
                        errors: error.message,
                      });
                    });
                } else if (extensionName === ".pdf") {
                  await sock
                    .sendMessage(exists.jid || exists[0].jid, {
                      document: {
                        url: nameFileSent,
                      },
                      caption: sendMessage,
                    })
                    .then((result) => {
                      res.send({
                        status: 200,
                        message: "Success",
                      });
                    })
                    .catch((error) => {
                      res.status(500).json({
                        status: 500,
                        message: "Internal server error",
                        errors: error.message,
                      });
                    });
                } else {
                  res.status(415).json({
                    status: 415,
                    message: `Unsupported media type`,
                  });
                }
              } else {
                res.status(401).json({
                  status: 401,
                  message: `Unauthorized: Whatsapp ${number} not exists`,
                });
              }
            } else {
              res.status(401).json({
                status: 401,
                message: "Unauthorized: Not connected",
              });
            }
          }
        }
      } else {
        res.status(401).json({
          status: 401,
          response: "Unauthorized: Invalid key",
        });
      }
    } catch (error) {
      res.status(500).json({
        status: 500,
        message: "Internal server error",
        errors: error.message,
      });
    }
  }
);

// send group message
app.post(
  "/send-group-message",
  [
    check("id_group").notEmpty(),
    check("message").notEmpty(),
    check("token").notEmpty(),
  ],
  async (req, res) => {
    const errors = validationResult(req);
    if (!errors.isEmpty()) {
      return res.status(400).json({
        status: 400,
        message: "Bad request!",
        errors: errors.array(),
      });
    }
    const sendMessage = req.body.message;
    const id_group = req.body.id_group;
    const token = req.body.token;
    const tokenKey = await db.getToken(token);
    let idgroup;
    let exist_idgroup;
    idgroup = groupIdFormatter(id_group);
    try {
      if (tokenKey === token) {
        if (isConnected) {
          if (!req.body.file) {
            if (!id_group) {
              res.status(400).json({
                status: 400,
                message: "Bad request!",
              });
            } else {
              let exist_idgroup = await sock.groupMetadata(idgroup);
              // console.log(exist_idgroup.id);
              // console.log("isConnected");
              if (
                exist_idgroup?.id ||
                (exist_idgroup && exist_idgroup[0]?.id)
              ) {
                sock
                  .sendMessage(idgroup, { text: sendMessage })
                  .then((result) => {
                    res.status(200).json({
                      status: 200,
                      response: "Success",
                    });
                  })
                  .catch((error) => {
                    res.status(500).json({
                      status: 500,
                      message: "Internal server error",
                      errors: error.message,
                    });
                  });
              } else {
                res.status(401).json({
                  status: 401,
                  message: `Unauthorized: Whatsapp ${idgroup} not exists`,
                });
              }
            }
          } else {
            if (!id_group) {
              res.status(400).json({
                status: 400,
                message: "Bad request!",
              });
            } else {
              exist_idgroup = await sock.groupMetadata(id_group);
              let saveFile = req.body.file;
              if (isConnected) {
                if (
                  exist_idgroup?.id ||
                  (exist_idgroup && exist_idgroup[0]?.id)
                ) {
                  let nameFileSent = saveFile;
                  let extensionName = path.extname(nameFileSent);
                  if (
                    extensionName === ".jpeg" ||
                    extensionName === ".jpg" ||
                    extensionName === ".png" ||
                    extensionName === ".gif"
                  ) {
                    await sock
                      .sendMessage(exist_idgroup.id || exist_idgroup[0].id, {
                        image: {
                          url: nameFileSent,
                        },
                        caption: sendMessage,
                      })
                      .then((result) => {
                        res.send({
                          status: 200,
                          response: "Success",
                        });
                      })
                      .catch((error) => {
                        res.status(500).json({
                          status: 500,
                          message: "Internal server error",
                          errors: error.message,
                        });
                        console.log("failed send message" + error);
                      });
                  } else if (
                    extensionName === ".mp3" ||
                    extensionName === ".ogg"
                  ) {
                    await sock
                      .sendMessage(exist_idgroup.id || exist_idgroup[0].id, {
                        audio: {
                          url: nameFileSent,
                          caption: sendMessage,
                        },
                        mimetype: "audio/mp4",
                      })
                      .then((result) => {
                        res.send({
                          status: 200,
                          response: "Success",
                        });
                      })
                      .catch((error) => {
                        res.status(500).json({
                          status: 500,
                          message: "Internal server error",
                          errors: error.message,
                        });
                      });
                  } else if (extensionName === ".pdf") {
                    await sock
                      .sendMessage(exist_idgroup.id || exist_idgroup[0].id, {
                        document: {
                          url: nameFileSent,
                        },
                        caption: sendMessage,
                      })
                      .then((result) => {
                        res.send({
                          status: 200,
                          response: "Success",
                        });
                      })
                      .catch((error) => {
                        res.status(415).json({
                          status: 415,
                          message: `Unsupported media type`,
                        });
                      });
                  } else {
                    res.status(415).json({
                      status: 415,
                      message: `Unsupported media type`,
                    });
                  }
                } else {
                  res.status(401).json({
                    status: 401,
                    message: `Unauthorized: Whatsapp ${number} not exists`,
                  });
                }
              } else {
                res.status(401).json({
                  status: 401,
                  message: "Unauthorized: Not connected",
                });
              }
            }
          }
          //end is connected
        } else {
          res.status(401).json({
            status: 401,
            message: "Unauthorized: Not connected",
          });
        }
      } else {
        res.status(401).json({
          status: 401,
          response: "Unauthorized: Invalid key",
        });
      }
      //end try
    } catch (error) {
      console.log(error);
      res.status(500).json({
        status: 500,
        message: "Internal server error",
        errors: error.message,
      });
    }
  }
);

connectToWhatsApp().catch((err) => console.log("unexpected error: " + err)); // catch any errors
server.listen(port, () => {
  console.log("Server running : http://127.0.0.1:" + port);
});
