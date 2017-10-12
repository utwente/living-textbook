# The-Living-Textbook

### Project essentials 
 
 * The project can be run locally after installation of the bower dependencies. You just need to clone it, run `bower install` and open `index.html`. In this case you use data embedded in the project. 
 * To create new data, you need to run `Sparql_to_JSON.html` and use a query to generate JSON you want. For this, you need a running triple store with some data. See the description bellow on how to get Parliament Triplestore up and running.

### Running the Living Textbook.

* Clone/download the repository 
* Run `bower install` (if you do not know bower, check it's [documentation](https://bower.io/)).
* Open `index.html` to see the page and the concept map.

### Updating the live server

* SSH to the server: `linux243.utsp.utwente.nl`, use your UT-account as credentials
* Move to `itc-giscience.utwente.nl/webroot`
* `git pull` to update the concept browser
* If necessary, update the bower components (see below).

**Data structure:**
* `data`: Contains the dokuwiki files and data
* `webroot`: Contains the GIT-clone of the Living Textbook concept browser 
> *Note*: In `webroot` two files (symlinks) are added, `doku` -> `../data/doku` & `doku.php` -> `dokuwiki/doku.php`.

**Dependencies:**

For the external dependencies `bower` is used, which unfortunately is not available on the UT-server. In order to install or update the components, copy the `bower_components` folder from your local machine into the `webroot` directory.

### Dokuwiki plugin
In the `dokuwiki` folder there is a plugin named `conceptbrowserlinker`, which is installed in dokuwiki. You can install this by zipping the folder and add it using the dokuwiki interface.

### Triplestore
The repository contains `Sparql_to_JSON.html`. If you run it you will have a simple interface/form to create and execute SPARQL queries. The response will be transformed into a proper JSON to build a concept map.    
Before you start playing with the code install some software: 

* You need the **Parliament Triple Store** (ver. 2.7.10) running locally. It can be downloaded [here](http://semwebcentral.org/frs/?group_id=159). This will be your backend. You can access the Administrator interface on [http://localhost:8089/parliament/](http://localhost:8089/parliament/) .
* In the Parliament triple store **create a named graph** with a URI http://living.map. This can be done as a SPARQL UPDATE. Paste `Create Graph <http://living.map>` in the query field, then click “Execute update”. See the print screen: 

![Creating a named graph](https://github.com/GIP-ITC-UniversityTwente/The-Living-Textbook/raw/master/printscreens/1.JPG)

* If you did first steps correctly, you are able to find the created graph on the “Explore” page of the triplestore’s Administrator  interface.
* The next step is  to switch off some inferencing rules implemented in the triplestore. Open “ParliamentConfig.txt” located in the folder with the Parliament installation. In “ParliamentConfig.txt”, you need to change lines 40-44. By default, those subclass and class inference rules are “on”. You need to change them to “off”. Restart Parliament. See the print screen:

![Switching off inference rules](https://github.com/GIP-ITC-UniversityTwente/The-Living-Textbook/raw/master/printscreens/2.JPG)

* Now you can upload the  data into the Named Graph you have just created. Go to the page “Insert Data” and use file insert to upload the data. 

Note: It is important to change the rules and restart the triple store before you upload the data. Otherwise, the triple store will apply those rules to the data on upload and the queries will yield unaccepted results. 





