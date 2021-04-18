<h1 align="center">
    <img height="200" src="https://img-0.journaldunet.com/mL_kyren1s51K39UUm-FZkcpKxw=/1280x/smart/0f11552fb07b481cb2fa21654a1bad70/ccmcms-jdn/11573128.jpg">
</h1>

# :earth_africa: Candotti Blog  
*Blog created in Symfony 4.4 as part of a Dev Back-End evaluation at Ynov Campus*  
  
- Symfony 4.4  
- Bootstrap 4
- PHP 7.2.5  

## How to clone/use the project
~~~bash
- git clone https://github.com/ecandotti/candotti-blog.git (Go inside the folder)
~~~
:warning: Don't forget to change .env value, turn on your Apache and MySQL ! :warning:  
~~~bash
- composer requirement  
- php bin/console doctrine:database:create  
- php bin/console make:migration  
- php bin/console doctrine:migrations:migrate 
- php bin/console doctrine:fixtures:load 
- symfony server:start  
~~~
  
### ID and Password (User)  
ID : user@candotti-blog.fr  
Password : password  
  
### ID and Password (Admin)  
ID : admin@candotti-blog.fr  
Password : password  
  
Enjoy :call_me_hand:
  
## To Do :  
- [X] Creation du projet  
- [X] Initialisation base de données  
- [X] Entité Article, User, Comment, Contact, Image, Like, Share, Newsletter
- [X] Newsletter feature  
- [X] Role, authentification  
- [X] CRUD Article  
- [X] Template Article  
- [X] Home Page  
- [X] About Page  
- [X] Contact Page  
- [X] Creation article  
- [X] Interface Admin  
- [X] Laisser un commentaire  
- [X] Temps de lecture    
- [X] Partager  
- [X] Interface utilisateur  
- [X] Afficher 5 derniers article aimé  
- [X] Afficher 5 derniers article partagé  
- [X] Liker  
- [X] Nombre de likes        
- [X] delete author variable        
- [X] Fix like / share double      
- [X] Ajouter une image  
- [X] Cancel button in edit mode   
- [X] Filtration (visible / non visible / none)   
- [X] Action de masse    
- [X] CKEditor  
- [X] Mot de passe oublié  
- [X] Changer son mot de passe  
- [X] Mise en place d'un captcha  
- [X] HTTPS force redirection    
- [X] Date de publication programmable    
- [X] Trier (titre, date)  
- [X] Secure publication (PublishAt give error)
- [X] Add hidden variable in Article entity    
- [ ] Search bar    
- [ ] Optimisation des requêtes    
- [ ] Trier (date) User  
- [ ] Consent Cookie    

