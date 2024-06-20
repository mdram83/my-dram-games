#My Dram Games
Repository for multiplayer board games application.

##Installation
When setting up repository locally make sure to complete below steps:
1. Define APP_URL. By default it is wsl.localhost for local development. If you want to use different name, make sure to update /.env and /.nginx/conf.d/nginx.conf (rewrite part).
2. Create selfsigned ssl cert 'nginx-dev-selfsigned.crt' and key 'nginx-dev-selfsigned.key' for wsl.localhost domain (or adjusted in point 1) and put them in /.nginx/certs folder.
3. Build docker image (docker compose).
4. Within mydramgames-app container run php artisan key:generate to generate .env file with APP_KEY and adjust remaining settings (see .env.example).
5. Within mydramgames-app container run npm and composer update to install required dependencies.

##Offline backlog

###Must/should/could have prior to release 1.0

R6 (M). REGISTRATION/LOGIN
M: Disable 'login' and 'register' features (frontend and backend)
S: Have 'register' based on family password and define 'Privacy Terms' section
C: Research and add proper security and process measures following GDPR, adjust 'Privacy Terms' section

R7 (M). COOKIES
M: Add Cookie consent feature

R22 (M). CI/CD
M: Backup database from old project before killing it, inform family
M: Setup Environment (domain, db, app server, https)
M: Setup CI/CD (GitHub Actions, pre and after hooks in repository)

R1 (S). LOGO
S: Replace template 'ANIME' logo with my own (all subpages)

R2 (S). CATEGORIES
S: Hide/Remove 'Categories' list from header and footer
C: Add 'Categories' feature to GameBox, Repository, Template(s)

R3 (S). BLOG
S: Remove 'Blog' link from header and footer

R4 (S). CONTACTS/ABOUT
S: Replace 'Contacts' with 'About' section

R5 (S). SEARCH
S: Hide/Remove 'Search' icon from top menu
C: Add 'Search' feature to GameBox, Repository, Template(s)?

R9 (S). SOCIAL LOGIN (after R6. REGISTRATION/LOGIN)
S: Hide/Remove social login buttons
C: Add social login features (decide which platforms)

R10 (S). TEMPLATE PURCHASE/COPYRIGHTS
S: Buy template and remove Copyright terms from footer
C: Adjust template to be tailwind based instead of pure css (will that be really easier) or minimize css/js libraries other way

R11 (S). HERO SECTION
S: Adjust hero section (text and images)
C: Adjust hero section and collect similar style images collection, locate such source

R12 (S). HOME PAGE GAMES LIST
S: Adjust home page games list (currently 'Trending Now') with game dedicated images. Remove unused elements from each Game
C: Add Tags feature and Played Games Count feature

R13 (S). VIEW ALL PAGE
S: Add 'View All' games page
C: Add Filtering feature and pagination

R14 (S). COMMENTS
S: Remove 'Comments' section from home page and gamebox page

R15 (S). HOME PAGE TOP VIEWS/MY GAMES
S: Hide/Remove 'Top Views' section from home page
C: Replace 'Top Views' section with 'Your Open Games' section (but use better name) and complete feature

R16 (S). HOME PAGE GAME PICTURE CLICKABLE
S: Make whole game picture clickable and sending to gamebox view

R17 (S). CATEGORIES BREADCRUMB
S: Hide/Remove breadcrumb from gamebox view
C: Breadcrumb to follow 'Categories' feature 

R18 (S). RATINGS
S: Hide/Remove hardcoded game rating info from gamebox
C: Add game rating feature

R19 (S). GAMEBOX PARAMETERS
S: Hide/Remove unused game box parameters from gamebox view
C: Analyze best and add gamebox parameters

R20 (S). GAMEBOX REVIEWS
S: Hide/Remove 'Reviews' section from Gamebox
C: Exchange 'Reviews' section with 'Your Open Games' section (see R15)

R21 (S). RELATED SECTION
S: Hide/Remove 'You might like' section from Gamebox
C: Add 'Related' feature and section to gamebox view to link to other similar games (based on sth...)

