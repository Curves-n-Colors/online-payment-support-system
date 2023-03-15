<div class="modal fade slide-up disable-scroll" id="categoryModal-{{ $row->id }}" tabindex="-1" role="dialog"
    aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content-wrapper">
            <div class="modal-content">
                <div class="modal-header clearfix text-left">
                    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                            class="pg-icon">&#10005;</i>
                    </button>
                    <h5>Edit Category <span class="semi-bold">Detail</span></h5>
                    <p class="p-b-10">We need category to add items in the subscription page.
                    </p>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action={{ route('categories.update',$row->id) }}>
                        @method('PUT')
                        @csrf
                        <div class="form-group-attached">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default required">
                                        <label>Category Name</label>
                                        <input type="text" name="name" class="form-control" required value="{{ $row->name }}">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check info">
                                    <input type="checkbox" name="is_active" value="10" id="checkbox-active" {{ $row->is_active==10?'checked':'' }}>
                                    <label for="checkbox-active">Active ?</label>
                                </div>
                            </div>
                            <div class="col-md-4 m-t-10 sm-m-t-10">
                                <button type="submit" class="btn btn-primary btn-block m-t-5">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>