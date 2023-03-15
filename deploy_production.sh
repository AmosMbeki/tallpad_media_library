#set application in maintanance mode                       
php artisan down                                           
                                                           
#update env file with prod env file                        
cp .env.prod .env                                          
                                                           
#generate key                                              
php artisan key:generate                                   
                                                           
#git pull for any changes changes                          
git pull                                                   
                                                           
#composer install changes                                  
composer install --no-dev --optimize-autoloader            
                                                           
#optimize application                                      
php artisan optimize                                       
php artisan route:cache                                    
                                                           
#clear cache                                               
php artisan cache:clear                                    
                                                           
#config clear                                              
php artisan config:clear                                   
                                                           
#migrate main application                                  
php artisan migrate                                        
                                                           
#migrate and seed if any seeders                           
php artisan db:seed                                        
                                                           
#set application up                                        
php artisan up                                             
