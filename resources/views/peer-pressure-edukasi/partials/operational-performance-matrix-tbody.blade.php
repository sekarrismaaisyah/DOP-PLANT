                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600" rowspan="2">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-tertiary-fixed-dim" data-icon="history">history</span>
                                 <span>Lagging Indicator</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">Incident</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, false) }}">{{ $peerOpMatrixFmt($c, 'Incident') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 text-xs text-slate-500 italic border-t border-slate-100">Accident</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(true, false) }}">{{ $peerOpMatrixFmt($c, 'Accident') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600" rowspan="2">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-tertiary-fixed-dim" data-icon="history">history</span>
                                 <span>Lagging Indicator</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">IFR</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, false) }}">{{ $peerOpMatrixFmtIf($c, 'IFR', 2) }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 text-xs text-slate-500 italic border-t border-slate-100">AFR</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(true, false) }}">{{ $peerOpMatrixFmtIf($c, 'AFR', 2) }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600" rowspan="2">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-secondary" data-icon="verified">verified</span>
                                 <span>Valid GR &amp; PSPP</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">GR</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, $peerOpMatrixCrit($c, 'gr')) }}">{{ $peerOpMatrixFmt($c, 'GR') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 text-xs text-slate-500 italic border-t border-slate-100">PSPP</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(true, $peerOpMatrixCrit($c, 'pspp')) }}">{{ $peerOpMatrixFmt($c, 'PSPP') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-slate-400" data-icon="visibility_off">visibility_off</span>
                                 <span>Blindspot TBC</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">Blindspot TBC</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, $peerOpMatrixCrit($c, 'blindspot')) }}">{{ $peerOpMatrixFmt($c, 'Blindspot TBC') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-error" data-icon="report_problem">report_problem</span>
                                 <span>Overdue Hazard</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">Overdue Hazard</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, $peerOpMatrixCrit($c, 'overdue')) }}">{{ $peerOpMatrixFmt($c, 'Overdue Hazard') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600" rowspan="2">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-primary" data-icon="map">map</span>
                                 <span>Coverage Area</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">All Area</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, $peerOpMatrixCrit($c, 'cov_all')) }}">{{ $peerOpMatrixFmt($c, $K['covAll']) }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 text-xs text-slate-500 italic border-t border-slate-100">Area Kritis</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(true, false) }}">{{ $peerOpMatrixFmt($c, $K['covKrit']) }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600" rowspan="2">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-blue-500" data-icon="task_alt">task_alt</span>
                                 <span>PJA Performance</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">PJA BC</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, false) }}">{{ $peerOpMatrixFmt($c, 'PJA BC') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 text-xs text-slate-500 italic border-t border-slate-100">PJA MK</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(true, $peerOpMatrixCrit($c, 'pja_mk')) }}">{{ $peerOpMatrixFmt($c, 'PJA MK') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-secondary" data-icon="pie_chart">pie_chart</span>
                                 <span>Ratio Pelaporan</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">Ratio Pelaporan TBC</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, $peerOpMatrixCrit($c, 'ratio')) }}">{{ $peerOpMatrixFmt($c, $K['ratioTbc']) }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 bg-slate-50/30 border-r border-slate-100 font-bold text-xs text-slate-600" rowspan="2">
                              <div class="flex items-center gap-3">
                                 <span class="material-symbols-outlined text-primary" data-icon="settings_remote">settings_remote</span>
                                 <span>Pengawasan Berjarak</span>
                              </div>
                           </td>
                           <td class="px-4 py-3 text-xs text-slate-500 italic">Real Time</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(false, $peerOpMatrixCrit($c, 'realtime')) }}">{{ $peerOpMatrixFmt($c, 'Real Time') }}</td>
                           @endforeach
                        </tr>
                        <tr class="transition-colors duration-200 ease-out hover:bg-indigo-50/[0.28]">
                           <td class="px-4 py-3 text-xs text-slate-500 italic border-t border-slate-100">Post Event</td>
                           @foreach ($peerOpMatrixCols as $c)
                           <td class="{{ $peerOpMatrixTd(true, $peerOpMatrixCrit($c, 'post')) }}">{{ $peerOpMatrixFmt($c, 'Post Event') }}</td>
                           @endforeach
                        </tr>
