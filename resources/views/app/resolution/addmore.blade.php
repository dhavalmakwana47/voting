<div class="row file-wrapper">
    <div class="col-4 mb-2">
        <select class="form-control" name="resolution[{{ $randomCount }}][option_type]">
            <option value="radio">Radio</option>
            <option value="checkbox">Checkbox</option>
        </select>
    </div>
    <div class="col-2 mb-2">
        <input class="form-control required-section" type="integer" min="1"
            name="resolution[{{ $randomCount }}][min]" placeholder="Min" required>
    </div>
    <div class="col-2 mb-2">
        <input class="form-control required-section" type="integer" min="1"
            name="resolution[{{ $randomCount }}][max]" placeholder="Max" required>
    </div>
    <div class="col-2 mb-2">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" 
                name="resolution[{{ $randomCount }}][skip]" value="1">
            <label class="form-check-label">Skip</label>
        </div>
    </div>
    <div class="col-2 mb-2">
        <button type="button" class="btn btn-danger required-section btn-sm"
            onclick="$(this).closest('.row').remove()">Delete</button>
    </div>
    <div class="col-6">
        <textarea class="resolution_description required-section" cols="60" rows="10"
            name="resolution[{{ $randomCount }}][description]"></textarea>
    </div>
    <div class="col-6">
        <input type="file" class="custom-file-input required-section"
            name="resolution[{{ $randomCount }}][resolution_files]" onchange="fileChange(this)">
        <label class="custom-file-label">{{ 'Choose file' }}</label>
        <br>
    </div>

    <div class="col-12">

        <div class="option-wrapper" id="option-div-{{ $randomCount }}">
            <div class="row">
                <!-- Initial options -->
                <div class="form-group option col-6">
                    <label for="option1">Option Name</label>
                    <input type="text" name="options[{{ $randomCount }}][0][name]" class="form-control" required>
                </div>
                <div class="form-group option  col-4">
                    <label for="option1">Image (jpg,png,jpeg,webp,gif)</label>
                    <input type="file" name="options[{{ $randomCount }}][0][image]" class="form-control" accept="jpg,png,jpeg,webp,gif">
                </div>
                <div class="form-group option  col-2">
                    <button type="button" class="btn btn-secondary mb-3" onclick="add_option({{ $randomCount }})">Add
                        More
                        Option</button>
                </div>
            </div>
        </div>
    </div>
</div>
