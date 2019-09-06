@extends('portal.layouts.sales-associate.master')

@section('page-title')Terms of Use @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-7">
                <h5 class="card-title">[Non-Exclusive] Sales Representative Agreement</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <h6>
                            
                            This Agreement is made on <b>{{ date('g:ia, l jS F Y', strtotime(Auth::guard('sales-associate')->user()->created_at)) }}</b> between <b>{{ Auth::guard('sales-associate')->user()->first_name." ".Auth::guard('sales-associate')->user()->last_name }}</b>, (the "Representative") and <b>Solushop Ghana</b>, (the "Company"). <br><br>The parties agree as follows 
                            </h6><br>
                            
                            <ol style="padding-left: 10px;">
                                <li>
                                    <h6>Appointment</h6>
                                    <b>Appointment of Representative</b> - The Company hereby appoints the Representative as a non-exclusive sales representative to sell and promote the Company's products and services (the "Products"). The Representative hereby accepts the appointment and agrees to represent and promote the sale of the Products on a non-exclusive basis.
                                    <br><br>
                                    <b>Territory</b> - During the Term, the Representative shall sell the Products only in Ghana (the "Territory")
                                </li><br>
                                <li>
                                    <h6>Commissions and Expenses</h6>
                                    <b>Commissions</b> - The Company hereby appoints the Representative as a non-exclusive sales representative to sell and promote the Company's products and services (the "Products"). The Representative hereby accepts the appointment and agrees to represent and promote the sale of the Products on a non-exclusive basis.
                                    <br><br>
                                    <b>Calculation of Commisions</b> - X% (2-4 based on sales team badge) of the Net Amount that the Company charges for orders of the Products placed through the Representative
                                    <br><br>
                                    <b>Offsets and Charge-Backs</b> - In calculating the Representative's commission, the Company may offset any credits, cancellations, refunds, allowances, and returns to or by customers of revenues on which Representative has already been paid commissions under this agreement, but in no event will the offset for any customer exceed the sales price of that customer's returned, cancelled, or otherwise credited Products.
                                    <br><br>
                                    <b>No Commissions in Certain Circumstances</b> - The Company will not be required to pay the Representative a commission in any of the following circumstances:
                                    if prohibited under applicable Law,
                                    if the Representative did not directly facilitate the sale of the Products to a customer,
                                    on any sales outside of the Territory,
                                    on any sales to Existing Customers, or  
                                    on any sale of Products to a customer occurring more than 5 days after the expiration or termination of this agreement, unless the sale is the direct result of the Representative's sales efforts before the termination or expiration.
                                    <br><br>
                                    <b>Expenses</b> - The Representative is solely responsible for any expenses it incurs in performing its services under this agreement.
                                    <br><br>
                                    <b>Definition of "Net Amount." </b> - In this agreement, "Net Amount" means the sales price of the sold product as listed on the applicable invoice, less charges for handling, freight, sales, use, value added, or similar taxes, import or export taxes or levies taxes, C.O.D. charges, insurance, customs duties, trade discounts, and any other fees or charges of any Governmental Authority.
                                </li><br>
                                <li>
                                    <h6>Payment Obligations</h6>
                                    <b>Timing of Payment</b> - The Company shall pay the Representative its commissions at month end, based on the amounts actually received. (For example, installment payments from a customer will result in installment commission payments to the Representative.)
                                    <br><br>
                                    <b>Taxes</b> - The Representative is solely responsible for paying all taxes incurred as a result of the performance of its services under this agreement and complying with all tax-related obligation. The Company has no obligation to pay or withhold any sums for taxes.
                                </li><br>
                                <li>
                                    <h6>Representative's Responsibilities</h6>
                                    <b>Duties</b> - The Representative shall devote such time, energy, and skill on a regular and consistent basis as is necessary to sell and promote the sale of the Company's Products in the Territory.
                                    <br><br>
                                    <b>Finalizing Orders</b> - The Representative shall assist in finalizing agreements and purchase orders with each customer, in form and substance satisfactory to the Company, for such customer's purchase of the Products.
                                    <br><br>
                                    <b>Stating Company Policies</b> - The Representative shall accurately represent and state Company policies to all present and potential customers.
                                    <br><br>
                                    <b> Sales-Related Services</b> - The Representative shall perform all other sales-related services as the Company may reasonably require.
                                    <br><br>
                                    <b>Maintaining Contact</b> - The Representative shall maintain contact with the Company via telephone, e-mail, or other agreed-upon means of communication with reasonable frequency to discuss sales activity within the Territory.
                                    <br><br>
                                    <b>Notice to Company</b> - The Representative shall give prompt Notice to the Company
                                    of all sales and orders,
                                    of any new companies or products that it represents at the time that it starts promoting those new companies and products,
                                    of any problems concerning customers (including Existing Customers), and
                                    if the Representative intends to advertise the Products outside of the Territory or solicit sales from customers located outside of the Territory.
                                    <br><br>
                                    <b>Compliance with Laws</b> - The Representative shall comply with all Laws and industry regulations relating to its representation of the Products.
                                    <br><br>
                                    <b>No Conflicting Representation</b> - The Representative shall not represent, promote, or otherwise try to sell in the Territory any lines or products that, in the Company's judgment, compete with the Products.
                                </li><br>
                                <li>
                                    <h6>Company's Responsibilities</h6>
                                    <b>Sales and Marketing Materials.</b> - The Company shall provide the Representative, at no cost, with sales and marketing materials relating to the Products.
                                    <br><br>
                                    <b>Sample Products</b> - The Company shall provide the Representative with current information as to improvements, upgrades, or other changes in the Products.
                                    <br><br>
                                    <b>Product Information</b> - The Representative shall accurately represent and state Company policies to all present and potential customers.
                                    <br><br>
                                    <b>Sales Terms</b> - The Company shall determine all Product prices and terms of sale, and give timely Notice to the Representative of any Product price changes.
                                </li><br>
                                <li>
                                    <h6>Term</h6>
                                    This agreement begun the day your profile was set up and holds until terminated by either party.
                                </li><br>
                                <li>
                                    <h6>Representative's Representations</h6>
                                    <b>No Conflicts.</b> - The Representative is under no restriction or obligation that may affect the performance of its obligations under this agreement.
                                    <br><br>
                                    <b>No Competing Representation</b> - The Representative does not currently represent or promote any products or services that compete with the Products.
                                </li><br>
                                <li>
                                    <h6>Acknowledgements</h6>
                                    <b>Non-Exclusivity</b> - The Company's appointment of the Representative is non-exclusive. The Company may appoint additional representatives in the Territory without liability or obligation to the Representative.
                                    <br><br>
                                    <b>No Other Compensation</b> - The compensation detailed in section 2 (Commissions and Expenses) is the Representative's sole compensation under this agreement.
                                    <br><br>
                                    <b>No Authority</b> - The Representative has no authority to bind the Company in any manner
                                    <br><br>
                                    <b>Right to Use Company Marks. </b> - The Representative's right to use the Company Marks derives solely from this agreement and is limited to performing its obligations under this agreement.
                                    <br><br>
                                    <b>Benefit of Goodwill</b> - The Representative's usage of the Company Marks and any resulting goodwill will accrue solely to the Company's benefit.
                                    <br><br>
                                    <b>No Obligation</b> - Nothing in this agreement creates any obligation between either party and any third party.
                                </li><br>
                                <li>
                                    <h6>Use of Company Marks</h6>
                                    <b>Ownership of Company Marks</b> - The Representative recognizes the Company's exclusive right, title, and interest in and to all service marks, trademarks, and trade names used by the Company (collectively, the "Company Marks").
                                    <br><br>
                                    <b>Actions in Company's Best Interests</b> - The Representative shall act in the best interests of the Company as owner of the Company Marks and in such a way as to preserve and protect the Company's interest in them.
                                    <br><br>
                                    <b>Protection of Company Marks</b> - The Representative shall not directly or indirectly
                                    <ol type="a">
                                        <li>register or use any other trade name, trademark, or service mark incorporating or based in whole or in part on any of the Company Marks,</li>
                                        <li>use any Company Mark as part of any corporate or trade name, as part of prominent signage displaying its business name, or in connection with unauthorized goods or services,</li>
                                        <li>use the Company Marks in combination with any other trademarks,</li>
                                        <li>debrand, rebrand, or private label any of the Company Marks,</li>
                                        <li>hold itself out as having any ownership interest in the Company Marks,</li>
                                        <li>engage in any conduct that would constitute Infringement of or otherwise affect either the Company's interest in the Company Marks or the goodwill associated with them,</li>
                                        <li>dispute the validity, ownership, or enforceability of any of the Company Marks,</li>
                                        <li>invalidate, dilute, or otherwise adversely affect the value of the goodwill associated with the Company Marks, or</li>
                                        <li>engage in any conduct that would constitute infringement of, or otherwise harm, the intellectual property rights of third parties.</li>
                                    </ol>
                                </li><br>
                                <li>
                                    <h6>Confidentiality</h6>
                                    <b>Confidentiality Obligations</b> - The Representative shall hold all Confidential Information in confidence in accordance with the terms of this agreement.
                                    <br><br>
                                    <b>Use only for the Purpose</b> - The Representative shall use the Confidential Information solely for the purpose of selling and promoting the Products.
                                    <br><br>
                                    <b>Definition of Confidential Information</b> - In this agreement, "Confidential Information" means all non-public business-related information, written or oral, disclosed or made available by the Company to the Representative, directly or indirectly, through any means of communication or observation, but does not include information that
                                    is or becomes publicly known through no wrongful act of the Representative,
                                    the Representative received in good faith on a non-confidential basis from a source other than the Company,
                                    was in the Representative's possession before its disclosure by the disclosing party or its Representatives,
                                    the Representative developed independently without breach of this agreement, or
                                    the Company has explicitly approved, by Notice to the Representative, for release to a third party.                                    
                                </li><br>
                                <li>
                                    <h6>Termination</h6>
                                    <b>Termination on Notice</b> - Either party may terminate this agreement for any reason upon 7 Business Days' Notice to the other party.
                                    <br><br>
                                    <b>Termination on Breach</b> - . If either party commits any material breach or material default in the performance of any obligation under this agreement, and the breach or default continues for a period of 3 Business Days after the other party delivers Notice to it reasonably detailing the breach or default, then the other party may terminate this agreement, with immediate effect, by giving Notice to the first party.
                                    <br><br>
                                    <b>Termination on Insolvency</b> - This agreement will terminate immediately upon either party's insolvency, bankruptcy, receivership, dissolution, or liquidation.        
                                </li><br>
                                <li>
                                    <h6>Effect of Termination</h6>
                                    <b>Return of Property</b> - Within 3 days of the termination or expiration of this agreement, the Representative shall return to the Company all the Company's property, and all documents relating to its representation of the Company, both originals and copies, under its direct or indirect control.
                                    <br><br>
                                    <b>Discontinue Use of Company Marks</b> - Effective as of the date of termination or expiration of this agreement, the Representative shall cease to use any of the Company Marks.
                                </li><br>
                                <li>
                                    <h6>Indemnification</h6>
                                    <b>Representative's Indemnity</b> - The Representative shall indemnify the Company and its Indemnitees against all claims, liability, and expenses (including legal fees) arising from any third party claim or proceeding brought against the Company that alleges any [grossly] negligent act or omission or willful conduct of the Representative or its Indemnitees.
                                    <br><br>
                                    <b>Company's Indemnity</b> - The Company shall indemnify the Representative [and its Indemnitees] against all claims, liability, and expenses (including legal fees) arising from any third party claim or proceeding brought against the Representative that alleges 
                                    any [grossly] negligent act or omission or willful conduct of the Company or its Indemnitees,
                                    any defects in the Products caused by the Company, or
                                    the Company's failure to provide any Products to a customer that were properly ordered through the Representative.                                   
                                    <br><br>
                                    <b>Conditions for Indemnification</b> - A party’s obligation to indemnify the other party under this section 13 (Indemnification) is conditional upon the indemnified party giving the indemnifying party prompt Notice of a claim or potential claim made against it,
                                    giving the indemnifying party sole control of the defense and settlement of the claim, except that the indemnifying party may not settle the claim unless the settlement unconditionally releases the indemnified party of all liability, and
                                    providing the indemnifying party with all reasonable assistance, at the indemnifying party’s expense, in connection with the claim.                                   
                                    <br><br>
                                    <b>Exception</b> - No party will be entitled to indemnification from the other party if the claim is based on or results in any material part from the negligence or unlawful or wrongful acts of the party seeking indemnification.
                                    <br><br>
                                    <b>Exclusive Remedies</b> - The indemnification rights granted under this section 13 (Indemnification) are the exclusive remedies available under this agreement in connection with the claims and losses that this section addresses.
                                    <br><br>
                                    <b>Definition of “Indemnitee.”</b> - In this agreement, “Indemnitee” means, for either party, any of that party’s directors, officers, employees, shareholders, partners, agents, or affiliates.
                                </li><br>
                                <li>
                                    <h6>General Provisions</h6>
                                    <b>Entire agreement</b> - This agreement contains all the terms agreed to by the parties relating to its subject matter. It replaces all previous discussions, understandings, and agreements.
                                    <br><br>
                                    <b>Amendment</b> - This agreement may only be amended by a written document signed by both parties.                             
                                    <br><br>
                                    <b>Assignment</b> - The Representative may not assign this agreement or any of its rights or obligations under this agreement without the Company's prior written consent. The Company may assign this agreement or any of its rights or obligations under this agreement, effective upon Notice to the Representative.
                                    <br><br>
                                    <b>No Partnership</b> - The Representative is an independent contractor. Nothing contained in this agreement creates a partnership, joint venture, employer/employee, principal-and-agent, or any similar relationship between the parties.                                  
                                </li><br>
                                <li>
                                    <h6>Notiice</h6>
                                    All notices and other communications between the parties must be in writing and addressed to management@solushop.com.gh.
                                    <br><br>                                
                                </li><br>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

