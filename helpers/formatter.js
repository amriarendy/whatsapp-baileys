const phoneNumberFormatter = function (number) {
  let formatted = number.replace(/\D/g, "");

  if (formatted.startsWith("0")) {
    formatted = "62" + formatted.substr(1);
  }

  if (!formatted.endsWith("@c.us")) {
    formatted += "@c.us";
  }
  return formatted;
};

const numberFormatter = function (number) {
  let formatted = number.replace(/\D/g, "");

  if (formatted.startsWith("0")) {
    formatted = "62" + formatted.substr(1);
  }

  if (!formatted.endsWith("@s.whatsapp.net")) {
    formatted += "@s.whatsapp.net";
  }
  return formatted;
};

const groupIdFormatter = function (id_group) {
  let formatted = id_group.replace(/\D/g, "");
  if (!formatted.endsWith("@g.us")) {
    formatted += "@g.us";
  }
  return formatted;
};

module.exports = {
  phoneNumberFormatter,
  groupIdFormatter,
  numberFormatter,
};
