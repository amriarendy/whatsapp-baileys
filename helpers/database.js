const mysql = require("mysql2/promise");
const fs = require("fs");
const path = require("path");
const axios = require("axios");
const moment = require("moment-timezone");

moment.tz.setDefault("Asia/Jakarta");
const currentDateTime = moment().format("Y-MM-DD HH:mm:ss");

const pool = async () => {
  return mysql.createPool({
    host: "localhost",
    user: "root",
    password: "",
    database: "wa_db",
    waitForConnections: true,
    connectionLimit: 10,
    maxIdle: 10, // max idle connections, the default value is the same as `connectionLimit`
    idleTimeout: 60000, // idle connections timeout, in milliseconds, the default value 60000
    queueLimit: 25,
    enableKeepAlive: true,
    keepAliveInitialDelay: 0,
    multipleStatements: true,
  });
};

async () => {
  try {
    const connection = await pool.getConnection();
    console.log("Connected to the MySQL server..");
    connection.realese();
  } catch (error) {
    console.error("Error connecting to the MySQL server:", error);
  }
};

if (pool.connection) {
  console.log("Database Unconnected");
} else {
  console.log("Database Connected");
}

const duplicateGroup = async (param) => {
  try {
    const connection = await pool();
    let [rows] = await connection.query(
      `SELECT contact_number FROM contacts WHERE contact_number = ?`,
      [param]
    );
    if (rows.length > 0) return rows[0].contact_number;
    await connection.end();
  } catch (error) {
    console.error("Error checked data duplicate:", error);
  }
};

const insertGroup = async (id_group, name_group) => {
  try {
    const connection = await pool();
    let data = {
      contact_number: id_group,
      contact_name: name_group,
      contact_type: "GROUP",
      contact_category: "uncategorized",
      user_id: "admin@admin.com",
      created_at: currentDateTime.toString(),
    };
    const [result] = await connection.query(`INSERT INTO contacts SET ?`, data);
    console.log("Insert data success, rows affected:", result.affectedRows);
    await connection.end();
  } catch (error) {
    console.error("Error inserting data:", error);
  }
};

// const duplicateGroup = async (param) => {
//   try {
//     const connection = await pool();
//     const sql =
//       "SELECT contact_number FROM contacts WHERE contact_number = ? LIMIT ?";
//     var data = [param, 10];
//     connection.query(sql, [data], function (err) {
//       if (err) throw err;
//       connection.end();
//     });
//   } catch (error) {
//     console.log("mysql process error: ".error);
//   }
// };

// const insertGroup = async (id_group, subject) => {
//   try {
//     const connection = await pool();
//     let group = "GROUP";
//     let user_id = "admin@admin.com";
//     let category = "uncategorized";
//     let timeStamp = currentDateTime.toString();
//     const sql =
//       "INSERT INTO contacts (contact_name, contact_number, contact_type, contact_category, user_id, created_at) VALUES ?";
//     var data = [id_group, subject, group, category, user_id, timeStamp];
//     connection.query(sql, [data], function (err) {
//       if (err) throw err;
//       connection.end();
//     });
//   } catch (error) {
//     console.log("mysql process error: ".error);
//   }
// };

const getAutoreply = async (keyword) => {
  const connection = await pool();
  const [rows, fields] = await connection.execute(
    "SELECT response, media FROM autoreplies WHERE keyword = ?",
    [keyword]
  );
  if (rows.length > 0) {
    const autoreply = {
      response: rows[0].response,
      media: rows[0].media,
    };
    const file = autoreply.media;
    let param;
    const existFile = async function (url) {
      try {
        const response = await axios.head(url);
        if (response.status === 200) {
          let extensionName = path.extname(autoreply.media);
          console.log(`this ext: ${extensionName} - ${file}`);
          if (
            extensionName === ".jpeg" ||
            extensionName === ".jpg" ||
            extensionName === ".png" ||
            extensionName === ".gif"
          ) {
            console.log("File is: image");
            param = "image";
          } else if (extensionName === ".mp3" || extensionName === ".ogg") {
            console.log("File is: audio");
            param = "audio";
          } else if (extensionName === ".pdf") {
            console.log("File is: document");
            param = "document";
          } else {
            return false;
          }
        } else {
          console.log(
            `File at ${url} returned status code ${response.status}.`
          );
          return false;
        }
      } catch (error) {
        if (error.response && error.response.status === 404) {
          console.log(`File at ${url} is missing.`);
          return false;
        } else {
          console.error(`Error checking ${url}: ${error.message}`);
          return false;
        }
      }
    };

    await existFile(file);
    return { autoreply, param };
  }
  return false;
};

const getToken = async (token) => {
  try {
    const connection = await pool();
    const [rows] = await connection.query(
      "SELECT token FROM setting WHERE token = ?",
      [token]
    );
    if (rows.length > 0) return rows[0].token;
    return false;
  } catch (error) {
    console.log(error);
  }
};

const getKey = async (api_key) => {
  try {
    const connection = await pool();
    const [rows] = await connection.query(
      "SELECT api_key FROM users WHERE api_key = ?",
      [api_key]
    );
    if (rows.length > 0) return rows[0].api_key;
    return false;
  } catch (error) {
    console.log(error);
  }
};

const getApi = async (api_key) => {
  try {
    const connection = await pool();
    const [rows] = await connection.query(
      "SELECT email, api_key FROM users WHERE api_key = ?",
      [api_key]
    );
    if (rows.length > 0) {
      const key = {
        email: rows[0].email,
        api_key: rows[0].api_key,
      };
      return key;
    }
    return false;
  } catch (error) {
    console.log(error);
  }
};

module.exports = {
  duplicateGroup,
  getAutoreply,
  insertGroup,
  getToken,
  getApi,
  getKey,
  pool,
};
