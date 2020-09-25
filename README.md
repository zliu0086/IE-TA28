# IE-TA28
IE PROJECT - TA28 - breathharbour.net - lung health

-- Introduction of website:

The website focus on how to help people who always cook Asian dishes (age 35-45) at indoor kitchen and with mild respiratory inflammation in Australia to reduce the amount of kitchen fumes to protect their lung health. When users comes to the website, firstly, they can view any information of kitchen fumes, respiratory inflammation and COPD (two kinds of lung disease), the prevention about lung diseases and related articles & latest news about kitchen fums & lung diseases. Secondly, users can obtain information from the graph about kitchen conditions which will affect people's lung health, a self-check to help users to check their preferences of cooking as well as provide them personal suggestions based on their answers. 

-- Motivation: Why build this website? What problems the website solve?

Kitchen smoke are comprised of particulate matter and toxic gases which will increase the occurrence of acute respiratory symptoms and several chronic illnesses. Most of people who cook Asian dishes have little awareness of negative impacts on kitchen smoke which may lead to lung diseases. Thus, our team found that most of people who cook Asian dishes have little awareness of negative impacts on kitchen smoke which may lead to lung disease. In addition, kitchen smoke is comprised of particulate matter and toxic gases which will increase the occurrence of acute respiratory infection and several chronic illnesses.

-- Website: https://www.breathharbour.net
-- password:lzy19960405

-- Tech used: server, plugin, security, database,

1.Our team used AWS bitnami service tobuild the website as the server

2.Our team used 'Draw attention' plugin to develop a kitchen graph which contain several objects of kitchen conditions in iteration 2 'know kitchen environment' page. When users click different objects, the left side of the graph will show related information bout that object.

3.Our team used 'WP Quiz' plugin to develop four quiz cards in iteration 1 'kitchen fumes' page to help uesrs check their understanding about kitchen fumes.

4.Our team used 'WP Clone' and 'All in one wp migration' plugins to backup each iteration.

5.Our team used 'Australia map' plugin to develop a interacted map in iteration 1 'respiratory inflammation' page to show related data for users.

6.Our team used 'XYZ PHP Code' plugin to put the php, js, html code in it.

7.Our team used 'Quiz and servey master' plugin to develop the self-check function for users in iteration 2 'self-check' page.

8.Our team used 'Flip box' plugin to created some flip cards which contain related information in iteration 2.

9.Our team used 'Defender' plugin to set the firewall, double authentication of the website.

10.Our team used 'password protected' plugin to set the user password when user access the website.

11.Our team input some commands to apply the ssl certificate and used 'SSL' plugin to help the website apply the certificate.

12.Our team used phpmyadmin to manage database as well as upload data onto it.

13.Our team created two interacted graph in iteration 2 to shows related data by using database and js,php & html code.

-- Features:

Thus, our team provide several functions for users: 
1. We improve awareness of Australian cooks by providing information of lung health &lung diseases & how to prevent that. (iteration 1)

2. We provide work-conditions check to help users know if their work environment suitable or not as well as cooks can obtain  suggestions which about which kind of work conditions they need pay more attention.(iteration 2)

3. Self-diagnosis test will help them know more about their body to give them a general idea of the extent of respiratory diseases they have. (iteration 3)

4. We provide a personal plan which include lung health exercises & recipe for users to help them know how should they do.(iteration 2&3)

-- Deployment: step of development

1.Creat and run the aws wordpress bitnami service to develop an delopment environment.

2.Buy the free domain name in the freenom website.

3.Use route53 service in AWS to bind the domain name with website IP address.

4.Set the password for the administrator if administrato login to the wordpress management page.

5.Set the basi information for creat the wordpress phpmyadmin database.

6.Enter commands for apply SSL certificate to ensure the website security.

7.Used 'Defender' plugin to set the firewall, double authentication of the website.

8.Used 'password protected' plugin to set the user password when user access the website.

9.Used 'WP Quiz', 'Australia map', 'visualizer' and other plugins as well as video, dropdown menu, etc. to develop iteration 1 function.

10.Used 'WP Clone' and 'All in one wp migration' plugins to backup iteration 1.

11.Built a new wordpress bitnami servide in Google Cloud Platform for iteration 1 back up. (because oue account only can have one free VM in AWS)

12.Login to the new wordpress management page and upload the iteration 1 backup.

13.Used new domain name to bind the iteration 1 backup.

14.Used 'Draw attention', 'XYZ PHP Code', 'Quiz and servey master' and other functions to build the iteration 2.

15.Used 'WP Clone' and 'All in one wp migration' plugins to backup iteration 2.

16.Built a new wordpress bitnami servide in Google Cloud Platform for iteration 2 back up. (because oue account only can have one free VM in AWS)

17.Used new domain name to bind the iteration 2 backup.
