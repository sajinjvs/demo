@extends('front.layout')

@section('pagename')
    - {{__('Modules One')}}
@endsection

@section('meta-description', !empty($seo) ? $seo->pricing_meta_description : '')
@section('meta-keywords', !empty($seo) ? $seo->pricing_meta_keywords : '')

@section('breadcrumb-title')
    {{__('Modules One')}}
@endsection
@section('breadcrumb-link')
    {{__('Modules One')}}
@endsection

@section('content')


<section class="saas-features pb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title-one section-title-two">
                                            </div>
                </div>
            </div>
            <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="features-item mb-40">

                            <h4>Dashboards</h4>
                            <p>Shadobooks offer an intuitive dashboard that helps you with a brief overview of all your sales pipelines, KPIs, lead management, project reminders, and invoice regulations. <br><br>

                            The dashboard is also developed to be intelligent to shed light on the accounting metrics that matter the most by presenting them in visually engaging formats through rich data correlation and statistical representations including widgets, pie charts and even data calendars.
                            <br><br>It is also developed to give you a clear picture of real-time cash flow that could save a lot of time, resources and operational costs incurred in handling multiple teams under multiple wings. <br><br>
                            Since they offer complete integration assistance, you can practically bring all your finance and accounting data under one roof.
                        </p>
  <a class="more"></a>
                        </div>

                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="features-item mb-40">

                            <h4>Myshadobooks Dashboard</h4>
                            <p>Shadobooks presents businesses with the Myoxygen Dashboard which presents all the financial data in a dynamically interactive format while also opening new doors to a wealth of insights that can help you with informed decision-making and improve your customer-facing strategies. <br><br>
                                The dashboard unifies all your sales, marketing and customer support data while also giving you the leverage to customize the presentation in ways you deem fit for your operational flow in easy clicks.<br><br>
                                You can organize all the KPIs in visual formats and spot trends through these interactive charts to drive your efforts in a better direction.<br><br>
                                Hosting an abundance of data and breaking them down to formats that are easier to interpret while also giving you complete interface freedom is why Shadobooks can be a game-changer for your business efforts.<br><br>
                                </p>
                        </div>
                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="features-item mb-40">

                            <h4>Customer Center</h4>
                            <p>The customer center segment of Shadobooks is where you can import all your client-facing data under one roof.<br><br>
                                Be it your partners from past projects or potential leads, you can integrate all their data here for a single panel source of view that will help in data decluttering.<br><br>
                                You can update all their contact information, communication pathways and all the courses of actions you have in mind for the leads down the pipeline here.<br><br>

                                Manage all your proposals, invoices, support tickets, upcoming tasks, project overviews, credit notes, reminders, subscriptions, orders or any interaction of any scale with your clients here.
                            </p>
                        </div>
                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="features-item mb-40">

                            <h4>Customer Loyalty</h4>
                            <p>By digging deeper into your customer behavior and understanding some key insights about their business with you, you can arrive at customer loyalty which is a measure of the relationship the client shares with you. <br><br>
                                An integral part of Shadobooks is presenting you with detailed insights about each customer. This data can be used for attracting customers even more through targeted incentives, nudging them towards their next sale with you and driving your efforts towards better overall customer management and retention.<br><br>
                                By keeping a thorough track of your interactions, you will also be maintaining a foundationally strong reputation management process with them.
                                </p>
                        </div>
                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="features-item mb-40">

                            <h4>Appointments</h4>
                            <p> The appointments management section of the dashboard is a key highlight of the dashboard where you can visualize all your upcoming events in the order of priority and timelines.<br><br>
                                This will help you maintain a coherent and well-informed customer management strategy. The meeting minutes can also be seamlessly added to the description column which will enable all the sales touchpoints to work in perfect sync with each other.<br><br>
                                This feature thus eliminates the chances of missing out on details, redundancy in communication, exhaustive paperwork or the need to operate with multiple applications.
                                </p>
                        </div>
                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="features-item mb-40">

                            <h4>Products</h4>
                            <p>An assorted list of all your products along with their availability, description, categorisation, subscription status and product link are all displayed here. <br><br>
                                There’s also specific grouping in the dashboard where you can see the invoiced products, subscriptions, product groups and product orders individually for better overall inventory management and process flow regulation..
                                </p>
                        </div>
                  </div>

                  <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="features-item mb-40">

                <h4>Subscriptions</h4>
                  <p>The subscription section of the dashboard helps you in keeping a tab on all of the software and tools to let the customer know the subscription status. Everything from your billing date, payment reminders, renewals, expiration and cancellation of plans are displayed here for you to keep a complete track of all your utility tools.</p>
                </div>
                </div>
                  <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="features-item mb-40">

                <h4>Leads</h4>
                <p>Shadobooks offers an easy-to-use CRM interface and facilitates data access from anywhere at any time. <br><br>
                    Drive better conversions by aligning your data under one roof. This section gives you complete authority to manage all the data pertaining to any lead including their names, phone numbers,  requirements, communication history and the nature of the lead. <br><br>
                    You can even note down the meeting minutes during your field visits so as to not miss any takeaways. Your sales pipeline will also work well in sync with the high level of organization that the dashboard offers.
                    </p>
                </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12">
    <div class="features-item mb-40">

        <h4>Suppliers</h4>
        <p>Shadobooks focuses on making your procurement process happen seamlessly and also easily identify the value of each vendor. Through flexible documentation of communication, it enables good supplier relationship management. <br><br>
            All your vendor list is updated here along with all the associated details like your purchases, pricing, overdue, invoice details, profile summary and their contact list. You can also add your purchase and payment reminders here to enable a prompt workflow.
            </p>
          </div>
</div>

<div class="col-lg-4 col-md-6 col-sm-12">
<div class="features-item mb-40">

<h4>Inventory</h4>
<p>Be it managing your orders, tracking your stock, keeping a tab on your warehouse operations, handling GST billing, and fulfilling orders on the go, Shadobooks helps you with comprehensive inventory management on the go. Stay informed about all your commodities and the numbers available, along with even their expiration dates, taxing, SKU codes, warehouse availability and order management. There’s also a wide scope of integrations with all your operational software tools to make it easy for you to manage all your operations under one roof. <br><br>
    With options to calculate loss and adjustments, check the inventory history and report generation with all necessary metrics, you can improve your overall inventory management efficiency through Shadobooks.
    </p>
  </div>
</div>
<div class="col-lg-4 col-md-6 col-sm-12">
<div class="features-item mb-40">

<h4>Fixed Assets</h4>
<p>The fixed asset section of the platform enables end-to-end visibility of the management process starting from acquisition to disposition. Shadobook offers an elaborate portal for complete tracking of asset movements across your enterprise, request management and also ensure compliance through periodical audits. You can easily maintain, monitor, and manage business assets along with maintenance reporting, audit compliance, licenses management and also asserting depreciation values. <br><br>
    By integrating all your crucial data under one platform, you can also hugely prevent any losses and damages.
    </p>
  </div>
</div>
<div class="col-lg-4 col-md-6 col-sm-12">
<div class="features-item mb-40">

<h4>Sales Area</h4>
<p>The sales area section of Shadobooks helps in performance analysis and forecasting to identify all the avenues where you can make better sales. All your order lists and sales channels can be thoroughly maintained along with options for elaborate report generation to identify key areas where you can make improvements. <br><br>
    Shadobooks also offers options for trade discounts management and diary sync where you can measure the direct profit from your sales and marketing approaches, thereby giving you valuable insights that you can use to level up your revenue.
    </p>
  </div>
</div>

<div class="col-lg-4 col-md-6 col-sm-12">
<div class="features-item mb-40">

<h4>Sales</h4>
<p>Help your customer-facing teams with sales solutions that can potentially accelerate their overall process efficiency in no time with Shadobooks. The sales interface enables hyper management of all your prospects, proposals, estimations, quotations, payments, invoices, credit notes and inventory management on the go. <br><br>
    Shadobooks also offer complete flexibility in organizing your sales touch points starting from lead capture to final conversion. The effective sales structure that the platform offers can help you scale your business towards better growth by streamlining your processes, preventing loss of data and offering you valuable insights along with end-to-end reporting of crucial metrics.
    </p>
  </div>
</div>
<div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-desktop" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Purchase</h4>
                <p>The purchase section of the CRM enables complete operation assistance when it comes to the end-to-end requirements of your procurement cycle. From creating a purchase requisition document to the final payment, you can automate your process flow completely.  <br><br>
                  The platform also offers options for thorough inventory management, developing electronic purchase orders and monitoring the flow of goods in real-time. Shadobooks also presents extensive features for vendor management, quotations, contracts and extensive report generation that will help you with better spending strategies.
                    </p>
            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-desktop" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Expenses</h4>
                <p>With Shadobooks, you can track, manage and strategies your spending as they happen with the real-time expense tracking solution that it offers. Make way for an easy administrative process with this streamlined CRM that makes it all the easier to record your business expenses, process receipts and request reimbursements all within just a few taps.<br><br>
                  With a flexible interface that is also accessible on the go, you can make your whole accounting process hassle-free. The CRM also presents elaborate reports that you can carefully assess to make better spending strategies and control your overall expenses.
                    </p>
            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Commission</h4>
                <p>Shadobooks comes with a commission management extension as a part of the CRM where you can automate the sales commission calculation for your respective teams. This section makes it all the quicker for managing bonuses and ensuring the distribution of data to the administrative executives so as to keep them all well-informed about the calculations.  <br><br>
                  Manual processing can result in errors and also cost you a lot of time and resources. Shadobooks CRM can simply help you carry these out systematically and with absolute precision.
            </p>
            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Contracts</h4>
                <p>Shadobooks CRM offers a curated section that is dedicated to maintaining and keeping track of all the agreements. Everything from the creation of contracts, overlooking business collaboration, managing documentation between the signed parties, and tracking policies and renewal are some of the features that the interface offers. <br><br>
                  The portal also offers options for obligation management, revisions and amendments, auditing and reporting and contract renewal reminders at the appropriate pre-set timelines. All of these help in better collaboration and ease of process management.
            </p>
            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Projects</h4>
              <p>Manage all your work under one roof with the project section of the CRM platform. The tool offers complete end-to-end support for taking care of all your project data including the tasks, notes, contracts, invoices, payments, support tickets and proposals altogether. The platform can greatly improve the organizational aspects of your operational processes and enhanced productivity levels.<br><br>
                 You can also integrate all your crucial documents under the attachment section. Keep a tab on complete project history, client profiling and everyday operations tracking in just a couple of clicks. The interface also has options for setting reminders or tracking deadlines and all the other add-ons that it comes with
            </p>
            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Project Roadmap</h4>
                <p>Shadobooks CRM presents an interface that opens doors to convenience by enabling complete operation support for projects of any scale. The roadmap section comes with a highly customisable interface that is designed for end-to-end data management and progress tracking.<br><br>
  There are separate sections for project overviews, maintaining task sheets or to-dos, timelines & schedules, meeting notes, milestones, raising tickets, or even managing your mind maps along with a whiteboard for charting down the best of your ideas then and there.<br><br>
  This section is also aimed at promoting collaborative team effort and has a tab with updated recent activities to keep all of the members informed and engaged transparently.</p>

            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Mind Map</h4>
                <<p>Project mind maps are integral in presenting your teammates with diverse perspectives and bringing their vision to life. The project road map section of the Shadobooks CRM helps you with a separate area to store, manage and access all your ideas from anywhere and at any time.<br><br>
Be it exploring different perspectives in any areas like clients, servicing, dealing with prospects or potential strategic partnerships, mind maps can prove to be an effective tool for enhancing collaboration among teams.</p>

            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Account Planning</h4>
              <p>Leverage the account planning section of the CRM to drive your business towards informed decision-making when it comes to cost management and planning revenues. This section has an abundance of options along with pre-build questionnaires that will help you understand your customers better.<br><br>
The interface can help you manage all your account planning information including the core values of the business, sales channels, marketing activities, overall background and business history along with separate columns for financial data incorporation. All of these will help you with a customer-centric approach and engage with your customers through a data-driven strategy. </p>

            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4> Tasks</h4>
                <p>Organize, build, collaborate and manage all your work under one place with the tasks section of the Shadobooks CRM. The solution it offers is a quick-to-implement and easy-to-use interface.<br><br>
  Manage, prioritize, access and oversee all your tasks on the go. Enhance your productivity levels with a well-suited tool that helps in complete project management from the start to finish. </p>
            </div>

        </div>

        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
            <div class="features-item mb-40">
                <h4>Task Bookmarks</h4>
                <p>Task bookmarks module presented by the Shadobooks CRM helps you categorize all your project tasks and assignments in the order of priority through bookmark lists. This enables easy visualization of your workflow and all your tasks can be grouped or searched in an orderly fashion.<br><br>
It improves collaboration between teams and also helps them stay informed on what’s happening around them. They help keep the teammates informed about all the task updates. Create, assign and manage tasks pertaining to both the client facing the end and internal work processes while also bookmarking them categorically.</p>

            </div>

        </div>


        <div class="col-lg-4 col-md-6 col-sm-12">
            <!--<div class="icon">
                <i class="fa fa-mobile" aria-hidden="true"></i>
            </div>-->
        <div class="features-item mb-40">
            <h4>OXY Task Filters</h4>
            <p>The custom OXY task filters help you extend the task functionality by helping you create and manage dynamic filter templates that you can fall back on for future use and easy access.<br><br>
This can help you filter tasks related to invoices, projects, contracts, estimates, expenses, leads and even proposal documents. Everything from the deadlines, assignee, task status and grouping are all possible in this section of the Shadobooks CRM. The task filters also enable you to assign fixed hourly rates and get a grasp of the total hours logged in along with the billable amount at any time during the flow of your projects.</p>

        </div>

    </div>

  <div class="col-lg-4 col-md-6 col-sm-12">
      <!--<div class="icon">
          <i class="fa fa-mobile" aria-hidden="true"></i>
      </div>-->
  <div class="features-item mb-40">
      <h4>Estimates Request</h4>
      <p>This section of the CRM helps you create forms that can be integrated into your website. These forms enable the user to get in touch with you and request an estimate about your services of yours that they’re looking to avail of.<br><br>
The estimate feature will give you the option to customize the form that is tailored to give you a better understanding of the customer intent. The CRM will enable assigning this influx of requests to specific teams and employees. The estimated requests can then be created here according to the details you have received and later send it to the respective clientele while also keeping a track of all your touch points.</p>

  </div>

</div>

<div class="col-lg-4 col-md-6 col-sm-12">
    <!--<div class="icon">
        <i class="fa fa-mobile" aria-hidden="true"></i>
    </div>-->
<div class="features-item mb-40">
    <h4>Accounting</h4>
    <p>Help your accountants, bookkeepers and tax professionals access all accounting data in one place. This all-inclusive finance platform will help you manage all your sales, revenues, profits, loss and other fiscal elements with absolute organization and precision.<br><br>
  With automated billing processes, flexible general ledger, integrated managing intelligence and easy access, you can streamline, simplify, visualize, analyze and automate the end-to-end of your accounting operations with ease. Keep track of all your accounts, credit notes, payments, expenses and transactions with a profit-loss overview.<br><br>
   The interface is also designed for helping accountants backtrack all the leads, prospects, and revenues for better strategizing in the future.</p>

</div>

</div>

<div class="col-lg-4 col-md-6 col-sm-12">
    <!--<div class="icon">
        <i class="fa fa-mobile" aria-hidden="true"></i>
    </div>-->
<div class="features-item mb-40">
    <h4>Reports</h4>
    <p>Shadobook CRM helps you with a thorough platform which enables you to generate extensive reports according to your requirements. The interface offers detailed report generation pertaining to the areas of sales, invoices, items, payments, credit notes, proposals, estimates, and HR payroll reports.<br><br>
  There are also charts that are generated with the data inputs for aiding better visualization of all your crucial metrics like income, revenues, payment and customer-related analysis. All this detailed record keeping will help you with simpler organization and in deriving insightful understanding that can help you drive towards better business goals.<p>

</div>
<a class="more"></a>
</div>

<div class="col-lg-4 col-md-6 col-sm-12">
    <!--<div class="icon">
        <i class="fa fa-mobile" aria-hidden="true"></i>
    </div>-->
<div class="features-item mb-40">
    <h4> HR Payroll</h4>
    <p>Shadobook CRM helps you with a thorough platform which enables you to generate extensive reports according to your requirements. The interface offers detailed report generation pertaining to the areas of sales, invoices, items, payments, credit notes, proposals, estimates, and HR payroll reports.<br><br>
  There are also charts that are generated with the data inputs for aiding better visualization of all your crucial metrics like income, revenues, payment and customer-related analysis. All this detailed record keeping will help you with simpler organization and in deriving insightful understanding that can help you drive towards better business goals.<p>

</div>
<a class="more"></a>
</div>

<div class="col-lg-4 col-md-6 col-sm-12">
    <!--<div class="icon">
        <i class="fa fa-mobile" aria-hidden="true"></i>
    </div>-->
<div class="features-item mb-40">
    <h4> Remainder</h4>
    <p>The remainder module of the shadobooks can help you keep complete track of all your deadlines, task reminders, and prevent any escalation. This section enables you to assign tasks along with their timelines as necessary. <br><br>
  This can help you take care of all your operations in an orderly format by maintaining an organized workflow. All your teams and departments will all be informed about your operations and the specific schedules assigned to them.</p>

</div>
<a class="more"></a>
</div>

<div class="col-lg-4 col-md-6 col-sm-12">
    <!--<div class="icon">
        <i class="fa fa-mobile" aria-hidden="true"></i>
    </div>-->
<div class="features-item mb-40">
    <h4> Staff Booking</h4>
    <p>Shadobooks CRM makes it easy for staff outsourcing and resource booking. This can help you manage your operations in an orderly fashion and never run out of your requirements by planning it ahead. This can also help you store all details with respect to your past hires along with their feedback and history.</p>

</div>
<a class="more"></a>
</div>








                            </div>
        </div>
    </section>



@endsection
