@php
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level ?? 0);
    if (isset($assetHead) && $assetHead == 'Current Asset') {
        $cAsset += $child['amount'];
    } elseif (isset($assetHead) && $assetHead == 'Fixed Asset') {
        $fAsset += $child['amount'];
    }
@endphp
<tr>
    <td>{!! $indent !!}<a href="{{ Route('admin.head-details.index') }}?id={{ $child['id'] }}">{{ $child['name'] }}</a></td>
    <td class="text-center" width="30">=></td>
    <td class="text-end">
        <a href="{{ Route('admin.head-details.index') }}?id={{ $child['id'] }}">
            {{ $child['amount'] >= 0 ? number_format($child['amount'], 2, '.', ',') : '(' . number_format(abs($child['amount']), 2, '.', ',') . ')' }}
        </a>
    </td>
</tr>

@if(!empty($child['childs']))
    @foreach($child['childs'] as $grand)
        @include('admin.reports.balance_sheet.partials.child_row', ['child' => $grand, 'assetHead' => $assetHead, 'level' => ($level ?? 0) + 1])
    @endforeach
@endif
