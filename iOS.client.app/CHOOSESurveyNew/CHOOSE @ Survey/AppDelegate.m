//  RHULSurveyApp
//  Adapted for Royal Holloway Choose-Survey Project by
//  Jamie Alnasir, 2015. Department of Computer Science for
//  Department of Economics.



#import "AppDelegate.h"
#import "ViewController.h"
#import <Google/CloudMessaging.h> // Google GCM libraries


@implementation AppDelegate

static NSString *const INSTANCE_ID_REGISTRATION_NOTIF_KEY = @"instance-id-token";
static NSString *const GCM_SENDER_ID = @"<your sender id>"; // @"123456"


- (void)DebugLog:(NSString *)log_title :(NSString *)log_msg {
// Jamie Alnasir, Implemented
// Debug logging with string parameters
    NSLog(@"%@ - %@", log_title, log_msg);
    
}

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
// Jamie Alnasir, Implemented
// On finished launching we need to initialise some HTTPS connection variables to the server as well
// as register for local iOS notifications. Also obtain a Google registration token for the device
// and register this with the RHUL server.
{
    // RHUL Choose-survey server, GCM registration URL and authentication credentials over HTTPS.
    RHUL_URL = @"https://path.to.your.server.domain.here/push/gcm-reg.php?reg_id=%@";
    RHUL_USER = @"YOUR_HTTPS_SERVER_USERNAME_HERE";
    RHUL_PASS = @"YOUR_HTTPS_SERVER_PASSWORD_HERE";
    GCM_PROJ_ID = @"YOUR_GCM_PROJECT_ID_HERE";
    
    NSLog(@"didFinishLaunchingWithOptions");
    
    // Register GCM token with RHUL server -- TESTING PHASE ONLY!!
    NSString *urlRHUL_GET = [NSString stringWithFormat:RHUL_URL, @"apple123"];
    [self registerGCM_reg:urlRHUL_GET];
    
    // Override point for customization after application launch.
    
    [launchOptions valueForKey:UIApplicationLaunchOptionsLocalNotificationKey];
    
    // Register for local notifications to show the alert bar message
    UIUserNotificationType types = UIUserNotificationTypeBadge | UIUserNotificationTypeSound | UIUserNotificationTypeAlert;
    UIUserNotificationSettings *mySettings = [UIUserNotificationSettings settingsForTypes:types categories:nil];
    [[UIApplication sharedApplication] registerUserNotificationSettings:mySettings];
    
    // Required for remote GCM
    [[UIApplication sharedApplication] registerForRemoteNotifications];
    
    UIUserNotificationType allNotificationTypes =  (UIUserNotificationTypeSound |
                                                    UIUserNotificationTypeAlert |  UIUserNotificationTypeBadge);
    UIUserNotificationSettings *settings =  [UIUserNotificationSettings
                                             settingsForTypes:allNotificationTypes categories:nil];
    [[UIApplication sharedApplication] registerUserNotificationSettings:settings];
    [[UIApplication sharedApplication] registerForRemoteNotifications];
    
    
    [self doLocalNotification:@"Test message!"]; // TESTING PHASE ONLY!!
    return YES;
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    // Connect to the GCM server to receive non-APNS notifications
    
    [[GCMService sharedInstance] connectWithHandler:^(NSError *error) {
        if (error) {
            NSLog(@"Could not connect to GCM: %@", error.localizedDescription);
        } else {
            _connectedToGCM = true;
            NSLog(@"Connected to GCM");
            // ...
        }
    }];
}


- (void)application:(UIApplication *)application
didRegisterForRemoteNotificationsWithDeviceToken:(NSData *)deviceToken {
// Jamie Alnasir, Implemented
// We have successfully registered and recieved a device token from GCM
    [self DebugLog:@"Got GCM device token" : deviceToken];
    
    // Register GCM token with RHUL server
    NSString *urlRHUL_GET = [NSString stringWithFormat:RHUL_URL, deviceToken];
    [self registerGCM_reg:urlRHUL_GET];
}

- (void) application:(UIApplication *)application didFailToRegisterForRemoteNotificationsWithError:(NSError *)error
{
    [self DebugLog:@"Error registering for GCM device token: " : error];
}

-(void)application:(UIApplication *)application didReceiveRemoteNotification:(NSDictionary *)userInfo
// Jamie Alnasir, Implemented
// Receive Remote Google Cloud Push Notification and re-send it as a local notification for display
// to the user
{
    [self doLocalNotification:userInfo];
}

- (void)application:(UIApplication *)application
didReceiveLocalNotification:(UILocalNotification *)notification {
// Jamie Alnasir, Implemented
// Handle Local notification by showing an alert if the app is running
// because local notifications are not shown if app is running.
    NSLog(@"Local iOS Notification received:");
    NSLog(notification.alertBody);
    [self doAlert:notification.alertBody];

}

- (void)registerGCM_reg:(NSString *)reg_str {
// Jamie Alnasir, Implemented
// Method to connect to Royal Holloway server and register
// the given Google cloud GCM device token
    
    NSURL *URL = [NSURL URLWithString:reg_str];
    NSURLRequest *request = [NSURLRequest requestWithURL:URL
                                             cachePolicy:NSURLRequestUseProtocolCachePolicy
                                         timeoutInterval:5.0];
    
    NSURLConnection *connection = [[NSURLConnection alloc] initWithRequest:request delegate:self];
    [connection start];
    //[connection release];
    
}


// NSURLConnection Delegates
- (void)connection:(NSURLConnection *)connection didReceiveAuthenticationChallenge:(NSURLAuthenticationChallenge *)challenge
// Jamie Alnasir, Implemented
// Handle authentication with RHUL server (should be over HTTPS for security purposes)
{
    if ([challenge previousFailureCount] == 0) {
        NSLog(@" Authentication challenge");
        NSURLCredential *newCredential = [NSURLCredential credentialWithUser:RHUL_USER
                                                                    password:RHUL_PASS
                                                                 persistence:NSURLCredentialPersistenceForSession];
        NSLog(@"Credential created");
        [[challenge sender] useCredential:newCredential forAuthenticationChallenge:challenge];
        NSLog(@"responded to authentication challenge");
    }
    else {
        NSLog(@"previous authentication failure");
    }
}

- (void)connection:(NSURLConnection *)connection didReceiveResponse:(NSURLResponse *)response {
    
}

- (void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data {
    
}

- (void)connectionDidFinishLoading:(NSURLConnection *)connection {
    
}

- (void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error {
    
}

-(void) doAlert:(NSString *) alert_str
// Jamie Alnasir, Implemented
// Display simple message dialog with alert_str subject
{
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Notification!"
                                                    message:alert_str
                                                   delegate:nil
                                          cancelButtonTitle:@"OK"
                                          otherButtonTitles:nil];
    [alert show];
}


-(void) doLocalNotification:(NSString *) msg_str
// Jamie Alnasir, Implemented
// Send local iOS notification to display msg_str
{
    UILocalNotification *notification = [[UILocalNotification alloc] init];
    
    // Schedule notification for NOW!
    notification.fireDate = [NSDate dateWithTimeIntervalSinceNow:0];
    notification.alertBody = msg_str;
    notification.timeZone = [NSTimeZone defaultTimeZone];
    notification.soundName = UILocalNotificationDefaultSoundName;
    notification.applicationIconBadgeNumber = 1;
    
    // Despatch Schedule via method
    [[UIApplication sharedApplication] scheduleLocalNotification:notification];
}


- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}
    

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

@end
