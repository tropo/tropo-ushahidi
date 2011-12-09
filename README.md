Tropo SMS Application for Ushaidi Incident Reporting
===================================================

An SMS application that can be used in conjunction with the Tropo platform to send incident reports to an Ushahidi instance.

Set Up and Configuration
===========================

* Clone the GitHub repo.
* Modify the file "submit-report.php" and add the specifics of your Ushaidi installation in the declared constants at the top of the file.
* Log in to your Tropo account (or [sign up for an account](https://www.tropo.com/account/register.jsp) - it's free).
* Create a new Tropo scripting application, and use the contents of the "submit-report.php" file as a new hosted file.  Detailed instructions here on [creating a new Scripting application in Tropo are here](https://www.tropo.com/docs/scripting/creating_first_application.htm).
* When your application is created, provision a new Voice + SMS number for your application.
* Optionally, add an IM address for your app.

See also
========

[Tropo + Ushahidi = Awesome!](http://blog.tropo.com/2011/12/09/tropo-ushahidi-awesome/) on the Tropo blog.