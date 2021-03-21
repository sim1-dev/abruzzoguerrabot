# AbruzzoGuerraBot - WIP (Beta 2)
Guerra testuale tra comuni abruzzesi - canale Telegram: @AbruzzoGuerra

Autore: @TeamBallo

Tecnologie Utilizzate: PHP 7 + PDO:pgsql

Base Dati: Ricostruita manualmente dai fogli ISTAT per il censimento delle denominazioni comunali

Licenza: Open Source (no uso commerciale)

L'applicazione non tiene alcuna traccia di dati personali dei suoi utilizzatori

Largamente ispirato da ItaliaGuerraBot


# Funzionamento

Ogni X minuti l'applicazione prende due comuni casuali e li fa scontrare tra loro. Il comune vincente guadagna un "punto peso" (territorio), mentre al perdente ne viene sottratto uno. 
Più peso un comune ha e più possibilità ha di attaccare. Una volta arrivato a 0 peso il comune viene automaticamente eliminato dal gioco.
Quando rimane un solo comune in gioco viene automaticamente dichiarato vincitore, e viene mostrata una classifica dei TOP 5 comuni con più uccisioni.

L'applicazione web è generalizzata e può essere usata anche per altri comuni (potenzialmente anche con tutti i comuni italiani cambiando il data source).


# Installazione

Assicurarsi che il proprio server soddisfi i seguenti requisiti:

- Webserver Apache/nginx con PHP > 7 con estensioni PDO e .env (con relativo file di configurazione con le variabili env)

- Database relazionale a scelta (potrebbero essere necessarie alcune modifiche se si usa un db diverso da PostGreSQL)

- crontab installato e configurato per l'esecuzione del file cron.php ogni X minuti a scelta

L'app possiede 2 metodi fondamentali, sendGETMessage e sendGETMessageToChannel.

È incluso anche un metodo sendMessageToRegno che può essere riadattato per eseguire l'invio del messaggio verso un canale Telegram aggiuntivo se necessario.

Anche se tutte le chiamate vengono effettuate manualmente, il bot è impostato per utilizzare il metodo webhook. 
Per settarlo sarà necessario un dominio con certificato SSL. Chiamare il metodo setWebhook inserendo nel link della chiamata l'indirizzo dell'app e il token del proprio bot (creabile con @BotFather).
