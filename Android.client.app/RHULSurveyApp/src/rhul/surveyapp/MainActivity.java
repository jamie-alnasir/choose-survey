package rhul.surveyapp;


import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.Toast;

import java.io.IOException;

import rhul.surveyapp.R;

import com.google.android.gms.gcm.GoogleCloudMessaging;
import android.os.AsyncTask;
import android.os.Bundle;

public class MainActivity extends Activity {

	private Button button;
	GoogleCloudMessaging gcm;
    String regid;
    String PROJECT_NUMBER = "YOUR_NUMERIC_GCM_API_ID_HERE"; // rhul.surveyapp GCM API ID
    
    public void ShowMsg(String aMsg)
    {    	
    	Toast.makeText(this, aMsg, Toast.LENGTH_LONG).show();
    }
    
    public void DebugMsg(String aMsg)
	{
		Log.w("RHUL Survey App", aMsg);
	}

	public void onCreate(Bundle savedInstanceState) {
		final Context context = this;		

		super.onCreate(savedInstanceState);
		setContentView(R.layout.main);			
		
		Intent intent = new Intent(context, WebViewActivity.class);
		startActivity(intent);
		
		getRegId();
		//ShowMsg("Hello!");
		
	}
	
	public void getRegId(){
        new AsyncTask<Void, Void, String>() {
            @SuppressWarnings("deprecation")
			@Override
            protected String doInBackground(Void... params) {
                String msg = "";
                try {
                    if (gcm == null) {
                        gcm = GoogleCloudMessaging.getInstance(getApplicationContext());
                    }
                    regid = gcm.register(PROJECT_NUMBER);
                    msg = "Device registered, registration ID=" + regid;
                    Log.w("GCM",  msg);
                    // No GUI calls here, use log.w/DebugMsg

                } catch (IOException ex) {
                    msg = ex.getMessage();
                    Log.w("GCM Error:",  msg);
                 // No GUI calls here, use log.w/DebugMsg
                }
                return msg;
            }

            @Override
            protected void onPostExecute(String msg) {
                //getRegId.setText(msg + "\n");
            }
        }.execute(null, null, null);
    }

}