//
//  ViewController.m
//  UIWebViewExample
//
//  Created by Karthik on 9/20/14.
//  Copyright (c) 2014 makemegeek. All rights reserved.
//
#import "ViewController.h"

@interface ViewController ()

@end

@implementation ViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
        [self loadurlAction: self];
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // After loading the view, and executing any ancestors viewDidLoad events
    // load the Choose-survey site
    
    [self loadurlAction: self];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
    
}

- (IBAction)loadurlAction:(id)sender
{
    NSMutableURLRequest * request =[NSMutableURLRequest requestWithURL:[NSURL URLWithString:@"https://choose-survey.royalholloway.ac.uk"]];
    [self.webView loadRequest:request];
}

- (IBAction)loadHtmlAction:(id)sender
{

        NSURL *url = [[NSBundle mainBundle] URLForResource:@"index" withExtension:@"html"];
        NSLog(@"%@",url);
            [self.webView loadRequest:[NSURLRequest requestWithURL:url]];
        

}

- (IBAction)loadDataAction:(id)sender
{
    NSURL *url = [[NSBundle mainBundle] URLForResource:@"sampleWord" withExtension:@"docx"];
    NSLog(@"%@",url);
    [self.webView loadRequest:[NSURLRequest requestWithURL:url]];
    
}

#pragma - mark UIWebView Delegate Methods
- (BOOL)webView:(UIWebView *)webView shouldStartLoadWithRequest:(NSURLRequest *)request navigationType:(UIWebViewNavigationType)navigationType
{
    NSLog(@"Loading URL :%@",request.URL.absoluteString);
    
    //return FALSE; //to stop loading
    return YES;
}

- (void)webViewDidStartLoad:(UIWebView *)webView
{
    
}

- (void)webViewDidFinishLoad:(UIWebView *)webView
{
    
}

- (void)webView:(UIWebView *)webView didFailLoadWithError:(NSError *)error
{
    NSLog(@"Failed to load with error :%@",[error debugDescription]);
    
}

@end
