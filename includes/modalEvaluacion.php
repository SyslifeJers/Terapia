<div class="modal fade" id="modalForm" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Info</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                <div class="modal-body">
                    <form action="#" class="form-validate is-alter" novalidate="novalidate">
                        <div class="form-group">
                            <label class="form-label" for="full-name">Full Name</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="full-name" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email-address">Email address</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="email-address" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone-no">Phone No</label>
                            <div class="form-control-wrap">
                                <input type="text" class="form-control" id="phone-no">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Communication</label>
                            <ul class="custom-control-group g-3 align-center">
                                <li>
                                    <div class="custom-control custom-control-sm custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="com-email">
                                        <label class="custom-control-label" for="com-email">Email</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-control custom-control-sm custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="com-sms">
                                        <label class="custom-control-label" for="com-sms">SMS</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="custom-control custom-control-sm custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="com-phone">
                                        <label class="custom-control-label" for="com-phone">Phone</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="pay-amount">Amount</label>
                            <div class="form-control-wrap">
                                <input type="number" class="form-control" id="pay-amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary">Save Informations</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <span class="sub-text">Modal Footer Text</span>
                </div>
            </div>
        </div>
    </div>