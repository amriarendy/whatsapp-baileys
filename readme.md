# Whatsapp Gateway Single Device

### Get Started

![](https://github.com/amriarendy/whatsapp-gateway/blob/single/public/files/Whatsapp%20Bot%20Api.png)

**Install Node Package**

```plaintext
npm update
```

**Import Database MySQL**  
Import database mysql and change connection.php and database.php at folder ./helpers.

**Change URL and PORT**  
Change url with your domain, on connection.php and index.js.

**Change Route Delete File**  
If you want to delete file after users to delete data, you must change or delete code on file: pages/media.php, pages/template.php, chatbot/autoreply.php, chatbot/log-sender.php.

```php
$fileOnly = str_replace("/whatsapp-bot-gateway/public/uploads/", '', $url['path']);
```

you change or delete on text \*whatsapp-bot-gateway\*.

**Setting Cronjob**  
Setting your cronjob for send message schedule and blasting message. best setting is every 1-5 minutes 50 messages. (Desclaimer: you have to setting limit send message for your whatsapp number balcklist).

```plaintext
*/5 * * * * php /home/your_directory/your_domain/helpers/cronjob.php
```

**Setting Your URL Gateway**  
Setting your URL Gateway to entrypoint your server (entrypoint node: index,js).

**Running Your Server**

Run your server with bash command.

```plaintext
npm start
```

**About me**

- [https://www.linkedin.com/in/amriarendy/](https://www.linkedin.com/in/amriarendy/)
- [https://www.instagram.com/amriarendy90/](https://www.instagram.com/amriarendy90/)
- [https://twitter.com/AmriaRendy](https://twitter.com/AmriaRendy)
