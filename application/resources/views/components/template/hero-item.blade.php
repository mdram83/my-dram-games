@props(['label'=>null, 'title', 'description', 'img', 'link'=>null, 'button'=>null, 'bgPositionTop'=>false])

<div class="hero__items set-bg" data-setbg="{{ $img }}" style="{{ $bgPositionTop ? 'background-position: top !important;' : '' }}">
    <div class="row">
        <div class="col-lg-6">
            <div class="hero__text">

                @isset($label)
                    <div class="label">{{ $label }}</div>
                @endisset

                <h2>{{ $title }}</h2>
                <p>{{ $description }}</p>

                @isset($link)
                    <a href="{{ $link }}"><span>{{ $button ?? 'More...' }}</span> <i class="fa fa-angle-right"></i></a>
                @endisset

            </div>
        </div>
    </div>
</div>
