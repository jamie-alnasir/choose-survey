//
//  AppDelegate.h
//  UIWebViewExample
//
//  Created by Karthik on 9/20/14.
//  Copyright (c) 2014 makemegeek. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface AppDelegate : UIResponder <UIApplicationDelegate>{
    NSString *RHUL_URL, *RHUL_USER, *RHUL_PASS, *GCM_PROJ_ID, *GCM_API_KEY;
    
    //void (void)registerGCM_reg:(NSString *)reg_str;
}

@property (strong, nonatomic) UIWindow *window;

// Google GCM properties
@property(nonatomic, strong) void (^registrationHandler)
(NSString *registrationToken, NSError *error);
@property(nonatomic, assign) BOOL connectedToGCM;
@property(nonatomic, strong) NSString* registrationToken;
@property(nonatomic, assign) BOOL subscribedToTopic;

@end
