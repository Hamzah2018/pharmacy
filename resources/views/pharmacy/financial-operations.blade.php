@extends('layouts.pharmacy/master')
@section('content')
  <x-alert type="status" />

  {{-- TODO STYLE PAGE (NAIF) 😅 --}}
  <main class="page-invoice-profile">

    <div class="t-wallet-wrapper t-card" style="justify-content: flex-start; gap: .5rem">
      <div class="t-wallet">
        <x-icon icon='wallet' />
        <span style="color: rgb(78 125 203)">رصيدك الحالي:</span>
        <span style="color: #3869BA">{{ $amount_not_confirmed }}</span>
      </div>

      <div class="t-wallet">
        <x-icon icon='wallet' />
        <span style="color: rgb(78 125 203)">الرصيد القابل للسحب:</span>
        <span style="color: #3869BA">{{ $amount_confirmed }}</span>
      </div>

      <div class="t-wallet">

        {{-- <x-icon icon='wallet' /> --}}
        <span style="color: rgb(78 125 203)"> الفواتير المؤكدة:</span>
        <span style="color: #3869BA">{{ $invoice_confirmed }}</span>
      </div>

      <div class="t-wallet">
        {{-- <x-icon icon='wallet' /> --}}
        <span style="color: rgb(78 125 203)"> الفواتير الغير مؤكدة:</span>
        <span style="color: #3869BA">{{ $invoice_not_confirmed }}</span>
      </div>


    </div>

    {{-- content --}}
    <div class="t-content" style="background: white">
      {{-- log data --}}
      <div class="t-log-data">
        <header>
          <x-font-icon icon='sack-dollar' />

          <h3 class="t-heading">@lang('heading.invoice-profile')</h3>
        </header>

        <div class="t-list">
          @if (isset($transactions))
            @foreach ($transactions as $transaction)
              {{-- item --}}
              <div class="t-item">
                {{-- header --}}
                <div class="t-item-header">
                  {{-- title --}}
                  <h4 style="display:flex; align-items: center;">
                    <a href="{{ $transaction->meta['invoice_id'] }}" style="color: #588FF4">
                      <span style="color: #3869BA">رقم العملية:</span> {{ $transaction->uuid }}
                      @if ($transaction->confirmed == 0)
                        <span class="badge badge-danger">غير مؤكدة</span>
                      @elseif($transaction->confirmed == 1)
                        <span class="badge badge-success">مؤكدة</span>
                      @endif
                    </a>
                  </h4>
                  {{-- date --}}
                  <span class="t-date" style="z-index: 2">
                    <span>تاريخ</span> {{ $transaction->created_at->format('Y-m-d') }}
                    <span> بتوقيت </span>{{ $transaction->created_at->format('h:m:s A') }}
                  </span>
                </div>

                {{-- desc --}}
                <div class="t-desc">
                  <p style="color: #456687">
                    <span>{{ $transaction->meta['state_1'] }}</span>
                    <span style="color: #588FF4">( {{ $transaction->meta['depositor'] }} )</span>
                    <span>{{ $transaction->meta['state_2'] }}</span>
                    <span style="color: #588FF4">( {{ $transaction->meta['recipient'] }} )</span>
                  </p>

                  <div style="margin-top: 16px">
                    <span style="color: #3869BA"> المبلغ: {{ $transaction->amount }}</span>
                  </div>

                </div>
                <div style="display:flex; justify-content: end">
                  <a href="{{ route('pharmacy.invoice', $transaction->meta['order_id']) }}" class="btn">عرض
                    الفاتورة</a>
                </div>
              </div>
            @endforeach
          @endif
        </div>


      </div>
    </div>

  </main>

@stop
