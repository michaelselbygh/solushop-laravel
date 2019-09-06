@extends('portal.layouts.vendor.master')

@section('page-title')Terms of Use @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-7">
                <h5 class="card-title">Terms Of Use on Solushop Ghana</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <h6>Below are the Terms of use for  <b>{{ Auth::guard('vendor')->user()->name }}</b> on <b>Solushop Ghana</b>. By continuing to use this platform you consent and agree to the terms listed below.</h6><br>
                            
                            <ol style="padding-left: 10px;">
                                <li>
                                    Products shall be uploaded by <b>{{ Auth::guard('vendor')->user()->name }}</b> and verified by <b>Solushop Ghana</b> prior to its appearance on the website. <b>Solushop Ghana</b> shall provide a portal for <b>{{ Auth::guard('vendor')->user()->name }}</b> to upload and manage products.
                                </li><br>
                                <li>
                                    <b>Solushop Ghana</b> reserves the right to reject or accept products uploaded for any reasonable ground or grounds and <b>{{ Auth::guard('vendor')->user()->name }}</b> shall be responsible to re-upload the product based on the recommendation of <b>Solushop Ghana</b>.
                                </li><br>
                                <li>
                                    The quality and quantity of the products uploaded shall be as per specification given by <b>{{ Auth::guard('vendor')->user()->name }}</b>. <b>{{ Auth::guard('vendor')->user()->name }}</b> shall bear any cost that may arise because of non-compliance.
                                </li><br>
                                <li>
                                    The delivery of the products shall be made by <b>Solushop Ghana</b> at a fee depending on the location of the customer and type of product. Delivery cost shall be made visible to (and born by) the customer prior to order.
                                </li><br>
                                <li>
                                    <b>Solushop Ghana</b> representative(s) shall inspect the goods at the pickup location and reserves the right to reject any product that is considered inferior quality to the quantities.
                                </li><br>
                                <li>
                                    The goods rejected by the representative(s) of <b>Solushop Ghana</b> shall be replaced by <b>{{ Auth::guard('vendor')->user()->name }}</b> and <b>{{ Auth::guard('vendor')->user()->name }}</b> shall bear all risks/costs of the materials rejected by <b>Solushop Ghana</b>.
                                </li><br>
                                <li>
                                    The transportation of the goods shall be made by <b>Solushop Ghana</b> on the same day as the quality control check by the representative(s).
                                </li><br>
                                <li>
                                    Representative (s) of <b>Solushop Ghana</b> will accompany the goods from the quality control check to the site of pick up. Any goods which are not accompanied by the representative(s) of <b>Solushop Ghana</b> will not be accepted as picked up.
                                </li><br>
                                <li>
                                    The cost of any damage to the products during transportation shall be the responsibility of <b>Solushop Ghana</b>.
                                </li><br>
                                <li>
                                    <b>{{ Auth::guard('vendor')->user()->name }}</b> reserves the right to update the quantity of items as when they feel necessary during the validity of this Agreement.
                                </li><br>
                                <li>
                                    <b>{{ Auth::guard('vendor')->user()->name }}</b> shall not without the consent in writing of <b>Solushop Ghana</b> assign or sub-let the contract or any part thereof, or make any agreement with any person/company for the execution of any portion of the supply. In this regard consent by <b>Solushop Ghana</b> will not relieve <b>{{ Auth::guard('vendor')->user()->name }}</b> from full and entire responsibility for this Agreement.
                                </li><br>
                                <li>
                                    <b>{{ Auth::guard('vendor')->user()->name }}</b> shall indemnify <b>Solushop Ghana</b> in respect of all claims, damages, compensation or expenses payable in consequence of any injury or accident caused by <b>{{ Auth::guard('vendor')->user()->name }}</b>.
                                </li><br>
                                <li>
                                    The custom duty, VAT or other Taxes and any other incidental charges, if required in connection to the sale of goods shall be borne by <b>{{ Auth::guard('vendor')->user()->name }}</b>.
                                </li><br>
                                <li>
                                    All Payment due {{ Auth::guard('vendor')->user()->name }} shall be made by <b>Solushop Ghana</b> Upon receipt and acceptance of the goods by the customer.
                                </li><br>
                                <li>
                                    If <b>{{ Auth::guard('vendor')->user()->name }}</b> shall in any manner neglect or fail to carry on the work or performance of the terms of the Agreement with due diligence or violates any of the terms of this Agreement <b>Solushop Ghana</b> shall be entitled to cancel The Agreement and demand damages.
                                </li><br>
                                <li>
                                    <b>{{ Auth::guard('vendor')->user()->name }}</b> shall not directly or indirectly display contact information on the website, product or on packaging to the customer. <b>Solushop Ghana</b> SHALL act as the intermediary between the customer and <b>{{ Auth::guard('vendor')->user()->name }}</b>.
                                </li><br>
                                <li>
                                    The terms of this Agreement shall be governed by the Laws of Ghana.
                                </li><br>
                                <li>
                                    If any dispute arises relating to or under this Agreement between the Parties hereto, the matter shall be referred to the Management Of <b>Solushop Ghana</b> and the decision shall be final, conclusive and binding upon both the parties.
                                </li><br>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

