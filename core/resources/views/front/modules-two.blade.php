@extends('front.layout')

@section('pagename')
    - {{__('Pricing')}}
@endsection

@section('meta-description', !empty($seo) ? $seo->pricing_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->pricing_meta_keywords : '')


@section('content')



<section class="saas-features pb-80">
        <div class="container">
            <div class="row">
              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4> Staff Workload</h4>
                  <p>Monitoring and overseeing staff workload will help you utilize your resources efficiently and improve overall productivity. Shadobook interface offers charts and graphs for visualizing your workflow, timelines, estimated hours, hours spent on particular work, department-wise workflow, etc. <br><br>
                The CRM also offer Kanban Charts for easy project management and workflow monitoring. This tool also helps in understanding the efficiency of your resources. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>HR Records</h4>
                  <p>Manage all your human resources and other related documents in a centralized database with the HR Records section of the Shadobooks CRM. The interface offers options for data management with respect to payrolls, onboarding processes, employee management, training modules, etc. <br><br>
              The interface is designed to offer statistical charts of the staff ratio by departments, age groups or job profile. In addition to employee management, they’ll also help you understand your resources better.<br><br>
              There are options for keeping track of HR records, birthdays, contracts, layoff checklists, organization charts and overall HR reports. All of these help in coordinating your HR efforts in a more orderly fashion. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4> Timesheets & Leave</h4>
                  <p>The timesheets section of the Shadobooks CRM helps you oversee all your work shift, leave management, shift categories, attendance and even reporting. All these help you reduce any gaps in your workflow and can help you get rid of age-old processes like paperwork or excel sheets to keep all these in check.<br><br>
                The reports generated in the section are detailed and you can export charts with respect to working hours statistics, leave by type, leave by departments and the ratio of check-in/out. There are also detailed aggregate reports generated with respect to annual leave, attendance, leave applications and check-in/out history.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Recruitments</h4>
                  <p>Manage the end-to-end of your recruitment process with Shadobooks CRM. Keep track of your candidates, their compatibility to the given job profile and their interview records here. <br><br>
                Get a complete visualization of your recruitment campaigns and their respective status. Get insightful updates about your candidates while also maintaining an up-to-date track of the interview schedules.<br><br>
                Improve your strategies with more insights about your recruitment channels and portals. The portal also offers Kanban boards for detailed visualization and understanding of your candidate profiles and their application status with your organization. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Objective</h4>
                  <p>Objective and Key Results (OKR) reports can help any organization in measuring, managing and tracking revenue-related goals. They help in understanding the core focus of businesses and drive your efforts towards meeting your business goals. <br><br>
              This section of Shadobooks CRM helps in performance management, revisiting strategies and making informed decisions to drive your business for the better.  The detailed report generated here will help in a better overall understanding of your processes and easy identification of further room for improvement in no time. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Support</h4>
                  <p>Power your support system and ticketing processes with Shadobooks CRM. The interface is easy to implement, use and scale as you deem fit.  All your ticket summaries, progress, active queries and other necessary information are crucial.<br><br>
              This section provides a single panel view for all your support requirements and helps in carrying out a transparent process between teams to be informed about the processes and tasks that happen around them. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Feedback</h4>
                  <p>Customer feedback management aids in managing better relationships with your clients by resolving all their queries in a fast manner. This will help you offer quick issue resolution.<br><br>
              Shadobooks helps you to keep a track of the feedback you receive and derive insights from these to level up your processes. This will also help you with overall reputation management and better optimisation & channeling of your efforts. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Idea Hub</h4>
                  <p>The idea hub section enables you with an out-of-the-box approach to focus on new opportunities. Be it any business challenge or issue resolution, you can jot down all your ideas here collectively and figure out the best approach from the options you have.<br><br>
                They help in the collaborative development of teams and overcoming crucial tasks by engaging in collective ideas. This section of the portal gives the employees a space to identify new challenges, come up with ideas driven by innovation and convert them to actual solutions. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Team Passwords</h4>
                  <p>The team passwords help you store all your passwords safely and securely. You can categorically store your crucial passwords for everything from credit col-lg-4 col-md-6 col-sm-12s, bank details, software licenses, email passwords and server credentials.<br><br>
              This section also offers an option to hide these from the client-facing end of the CRM. You can also manage the permissions to this section internally between teams to authorize access only to the ones you deem fit thus protecting this data even more so. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>File Sharing</h4>
                  <p>Shadobooks CRM offers a centralized platform to share and receive files as necessary with the clients or for internal sharing purposes. In order to prevent unauthorized access to specific crucial documents, the portal offers options to manage file sharing with permissible access and download restrictions.<br><br>
                The section also comes with a reporting option that can help you visualize the shared files and their respective download statistics. The file sharing option in the CRM can simply improve collaborative working between the team while also establishing a transparent process flow. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Spreadsheet Online</h4>
                  <p>Shadobooks CRM offers you an option to create, manage and work with all your spreadsheets within the portal. You can also upload your existing files and pick up where you left off by directly working on these here in the section.<br><br>
                You can seamlessly share these spreadsheets across your team or to the clients. There are also options for exporting and downloading the sheets. This section will minimize working on multiple platforms by bringing it all under one portal. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Setup</h4>
                  <p>The setup section helps you customize and configure the entire scope of the Shadobooks CRM. There are diverse operations here that you are enabled to do including: <br><br>
              ·       Adding new staff members,<br><br>
              ·       Creating client profiles<br><br>
              ·       Importing leads from sources<br><br>
              ·       Email, SMS or WhatsApp chat integrations<br><br>
              ·       Creating new roles<br><br>
              ·       Configuring theme styles and creating new templates<br><br>
              ·       Finance settings like tax rates, mode of payments and currencies<br><br>
              ·       Adding pre-defined responses, ticket management strategies and spam filters for support services<br><br>
              ·       Creating new custom fields<br><br>
              ·       Creating groups for idea hubs, whiteboards, mind maps, etc among other settings.<br><br>
              The setup section focuses on helping you manage the CRM your way. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Video Library</h4>
                  <p>The video library helps you to store and manage all your multimedia files in one place. There are options for you to download or share them at any point in time from anywhere.<br><br>
                Since the CRM offer extensive storage, you can simply use this section to manage all your resources without having to worry about the storage space that you have left. </p>


              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Webhooks</h4>
                  <p>Shadobooks CRM offers webhooks services that will allow HTTP requests for connecting web APIs and services with a subscribe or publish model. They are user-defined HTTP callbacks that are activated by a trigger to make a request.<br><br>
              A real-world example would be to set an automatic email reminder first thing each morning for your day-to-day task lists.</p>
              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>API</h4>
                  <p>Shadobooks API helps your developers seamlessly integrate any application into the CRM. This will help you with easy cross-platform working and better overall management of your operations by visualising all of it in one place. Seamlessly connect all your systems, software or app in your tech stack with Shadobooks easily!</p>
              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>HRM</h4>
                  <p>Shadobooks offer extensive support for human resource management within the portal. Everything from your employee data, onboarding documents, payrolls, record keeping, attendance, KPIs, payslips, taxes, contracts, training, etc can be managed all together within the CRM.<br><br>
                The extensive reports generated with respect to HR can also help you assess performance, and calculate compensations, deductions or incentives as per your requirements in easy steps.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Wikibooks</h4>
                  <p>The Wikibooks section of the Shadow books CRM offers an option for you to post articles for internal circulation or link them to other website sources. These articles can also be channelised for grasping the attention of specific staff roles. There are options for you to add attachments to these articles. You can also bookmark articles for referring to them later easily.</p>
              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Manufacturing</h4>
                  <p>The manufacturing section comes with a statistical dashboard that offers a graphical representation of your manufacturing orders in terms of the measures and period. This also comes with feature-rich options like product management, product variants, bills of materials and routing.<br><br>
                This easy-to-access interface can help your business keep a thorough record of your manufacturing process, their transit and completing your work orders at proper timelines.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>@Mention</h4>
                  <p>The CRM offers options for you to tag and mention someone to any section of the portal. There is also an option to use # for referring to any object such as project, tasks, contract, invoices or other support sections.<br><br>
              This will help all your employees who have access to the Shadobooks CRM to be on the loop with every task or to-do.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Whiteboard</h4>
                  <p>Shadobooks CRM has a separate whiteboard feature which can help you write down ideas, visualize your thoughts, explore new concepts and plan a lot of things in one. The section also offers options to categorize your ideas into separate boards and work your ideas more effectively.<br><br>
                There are also options to export these whiteboards via emails when necessary thus making it all the more accessible. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Knowledge Base</h4>
                  <p>The knowledge base helps you to create and manage technical write-ups, articles or announcements that you want to circulate internally.<br><br>
                This section is especially helpful for you if you regularly come across technical issues. Once you arrive at a solution, you can document it here and the others can refer to this on the go when they deal with similar situations anytime in the future. The wealth of information here will help all your individuals stay ahead of their respective processes by having complete documentation in place.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Chats</h4>
                  <p>Shadobooks CRM offers a live chat option with all your internal teams, group and even clients under one roof. This integrated live chat feature will also eliminate the need for multiple communication channels like email by bringing it all under one portal.<br><br>
                The chat section will thus make way for fast and easy updates of any process as and when you need it. This will help your entire team stay connected anywhere and at any time.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Mailbox</h4>
                  <p>Mailbox is one of the essential components of the Shadobooks CRM that will enable you to configure your email within the portal. Having an active mailbox within the CRM will eliminate the need for working with multiple tools by bringing everything under one roof.<br><br>
              This will help you with a quicker workflow and easy access to any information you’re looking for all within the portal itself.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Add-On SMS</h4>
                  <p>This section presents an option to send custom SMS messages to all your clients, employees, customers or leads. There is also a feature for creating SMS templates that you can directly send as necessary.<br><br>
              This will help you stay connected with your leads with multiple touch points and keep your employees in the loop with any new announcements that you might have internally. The SMS history will also be available here for you to keep track of all the past communication.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Custom E-mail/SMS</h4>
                  <p>Shadobooks CRM offers a dedicated section where you can create custom templates for sending emails or SMS to your clients, leads, and customers or even circulate internal announcements.<br><br>
                These automated texts will help you engage with your customers better and reach out to them personally with your messaging. You can even create and manage personalized marketing campaigns or sale outreach with this section of the CRM.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Zoom Meeting</h4>
                  <p>Shadobooks CRM offers an option for integration with the zoom conferencing service. Enjoy a seamless meeting experience with options to create new meetings or invite attendees from the portal itself.<br><br>
              There’s also an option for you to keep a recording of all your past meetings and their history for you to refer them at any point in time. There’s also an option for you to set reminders for any meeting that you create and start them whenever with just a click. The integrated email system and the chat section will also enable you to share these meeting links from within the portal itself.</p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>My To Do</h4>
                  <p>Capture everything from your latest task remainders, appointments or other business to-dos in this section of the Shadobooks CRM. You can simply check the box of any to-do to mark them completed.<br><br>
              The to-do section also stores a history of your past to-dos for you to keep track of all your previous tasks. You can also assort your to-dos based on categories in this section. All these will enable you to improve your workflow by not missing out on any crucial tasks and perfectly meet your assigned deadlines. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Utilities</h4>
                  <p>The utilities section of the CRM offers multiple options like media storage, bulk pdf exports, calendars, announcements, activity logs, surveys, ticket pipe logs and CSV exports.<br><br>
              The media storage section enables you and your team to store any multimedia files of even bigger sizes for easy accessibility. Bulk PDF exports help you with the export of crucial business data including invoices, payments, estimates, credit notes, proposals and expenses.<br><br>
              The calendar section helps you visualize all your upcoming appointments, meetings, or other reminders. The announcement section helps your spread any message across your internal teams and even your clients.<br><br>
              The goals section enables you to create any vision that you want to accomplish. There’s also an option to notify all your team members when you achieve or fail the goal. This helps drive combined efforts towards a common purpose.<br><br>
              The activity log helps every one of your team members stay in the loop and understand every last activity that’s happening within the CRM.<br><br>
              There’s an option for creating company-wide surveys to understand your employees’ take on different matters. Ticket pipe logs for visualizing your support processes and CSV exports of contracts, payments, invoices, etc are also possible. </p>

              </div>

              </div>

              <div class="col-lg-4 col-md-6 col-sm-12">
                  <!--<div class="icon">
                      <i class="fa fa-mobile" aria-hidden="true"></i>
                  </div>-->
              <div class="features-item mb-40">
                  <h4>Custom E-mail/SMS</h4>
                  <p>Shadobooks CRM offers a dedicated section where you can create custom templates for sending emails or SMS to your clients, leads, and customers or even circulate internal announcements.<br><br>
                These automated texts will help you engage with your customers better and reach out to them personally with your messaging. You can even create and manage personalized marketing campaigns or sale outreach with this section of the CRM.</p>

              </div>

              </div>




                            </div>
        </div>
    </section>



@endsection
