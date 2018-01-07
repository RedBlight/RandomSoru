# RandomSoru

RandomSoru is a random educational question generation engine. It generates random questions from questions templates.

I made it back in 2013 when I got bored of studying for university exams, solving questions, which were redundantly
mostly same as each other with different parameters.

I thought, these questions can be generalised and expressed with a template, and their variations then can be generated
 by a computer using random variables. I got very excited and started working on this project instead of studying for university exams.
 
## How does it work?

This a website that users can solve random questions from the topics available in the site. When user selects a topic,
they get a random question of that topic generated for them from question templates, and then displayed to them.

Question templates can be added from the admin panel, by the admin.

To make a question template, first, you define some random variables using PHP:
![Alt text](img/ss1.png "PHP")

Then, you write the details (graphics, question texts, answers etc.) of the question using SVG, HTML and LaTeX.
You can use the PHP variables here by writing the variable name in brackets, like this: {var}
![Alt text](img/ss2.png "PHP")

When generating the question, system will replace {var} with the corresponding value of the variable.
Here is an example of a generated question:
![Alt text](img/ss3.png "PHP")

Here is a generated question from another topic:
![Alt text](img/ss4.png "PHP")

And more and more generated variants of the same question template:
![Alt text](img/ss5.png "PHP")

## How to set it up?
1) Create the MySQL database with the given .sql file.
2) .sql file already has some entries in it, clean them as you wish.
3) Modify the necessary places in the code for the database credentials.
4) You need to have a ReCaptcha account, and write your private key to the Class_Database.php. 
5) Put all the files to your server, everything works correctly within the PHP version 5.3.26.
