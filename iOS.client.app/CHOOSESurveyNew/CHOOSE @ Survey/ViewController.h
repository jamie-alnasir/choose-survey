//
//  ViewController.h
//  RHULSurveyApp
//
//  Created by Karthik on 9/20/14. (UIWebView)
//  Copyright (c) 2014 makemegeek. All rights reserved.
//
//  Adapted for Royal Holloway Choose-Survey Project by
//  Jamie Alnasir, 2015. Department of Computer Science for
//  Department of Economics.

#import <UIKit/UIKit.h>

@interface ViewController : UIViewController
@property (weak, nonatomic) IBOutlet UIWebView *webView;

- (IBAction)loadurlAction:(id)sender;
- (IBAction)loadHtmlAction:(id)sender;
- (IBAction)loadDataAction:(id)sender;

@end
