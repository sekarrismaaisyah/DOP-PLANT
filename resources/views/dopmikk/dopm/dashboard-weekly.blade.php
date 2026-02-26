@extends('layouts.masterDopm')

@section('title', 'DOPM & IKK - Dashboard')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Filter Dropdown Styles */
    .btn-filter {
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn-filter:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #111827;
    }
    
    .btn-filter:focus {
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }
    
    .btn-filter.dropdown-toggle::after {
        margin-left: 0.5rem;
    }
    
    .dropdown-menu {
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
    }
    
    .dropdown-item.filter-option {
        padding: 0.5rem 1rem;
        transition: all 0.15s ease;
    }
    
    .dropdown-item.filter-option:hover {
        background-color: #f3f4f6;
        color: #111827;
    }
    
    .dropdown-item.filter-option:active {
        background-color: #e5e7eb;
    }
    
    /* Layer Toggle Button Styles */
    .layer-toggle-btn {
        position: relative;
    }
    
    .layer-toggle-btn.active {
        background-color: #3b82f6;
        color: #ffffff;
        border-color: #3b82f6;
    }
    
    .layer-toggle-btn.active:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }
    
    .layer-toggle-btn:not(.active) {
        background-color: #ffffff;
        color: #6b7280;
        opacity: 0.7;
    }
    
    .layer-toggle-btn:not(.active):hover {
        background-color: #f9fafb;
        opacity: 1;
    }
    
    /* Map Sidebar Panel Styles */
    .map-sidebar {
        position: absolute;
        top: 0;
        right: 0;
        width: 380px;
        height: 100%;
        background: #ffffff;
        box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex;
        transition: transform 0.3s ease;
        transform: translateX(0);
    }
    
    .map-sidebar.collapsed {
        transform: translateX(calc(100% - 50px));
    }
    
    .sidebar-toggle-btn {
        position: absolute;
        left: -40px;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 60px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-right: none;
        border-radius: 8px 0 0 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 1001;
        box-shadow: -2px 0 4px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-toggle-btn:hover {
        background: #f9fafb;
        box-shadow: -2px 0 6px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-toggle-btn i {
        font-size: 24px;
        color: #6b7280;
        transition: transform 0.3s ease;
    }
    
    .map-sidebar.collapsed .sidebar-toggle-btn i {
        transform: rotate(180deg);
    }
    
    .sidebar-content {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .sidebar-tabs {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e5e7eb;
        padding: 8px;
        gap: 4px;
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8f9fa;
    }
    
    .sidebar-tabs::-webkit-scrollbar {
        height: 6px;
    }
    
    .sidebar-tabs::-webkit-scrollbar-track {
        background: #f8f9fa;
    }
    
    .sidebar-tabs::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .sidebar-tabs::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .sidebar-tab {
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 16px;
        background: transparent;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        min-height: 70px;
        min-width: 80px;
        max-width: 120px;
    }
    
    .sidebar-tab:hover {
        background: #e9ecef;
    }
    
    .sidebar-tab.active {
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-tab i {
        font-size: 24px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    
    .sidebar-tab.active i {
        color: #3b82f6;
    }
    
    .sidebar-tab .tab-label {
        font-size: 12px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 4px;
    }
    
    .sidebar-tab.active .tab-label {
        color: #111827;
        font-weight: 600;
    }
    
    .sidebar-tab .tab-count {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        background: #e5e7eb;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 24px;
        text-align: center;
    }
    
    .sidebar-tab.active .tab-count {
        background: #3b82f6;
        color: #ffffff;
    }
    
    .sidebar-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    .sidebar-search {
        display: flex;
        align-items: center;
        padding: 12px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        gap: 8px;
    }
    
    .sidebar-search .search-icon {
        color: #9ca3af;
        font-size: 20px;
    }
    
    .sidebar-search-input {
        flex: 1;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease;
    }
    
    .sidebar-search-input:focus {
        border-color: #3b82f6;
    }
    
    .sidebar-filter-btn {
        background: transparent;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .sidebar-filter-btn:hover {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    
    .sidebar-filter-btn i {
        font-size: 20px;
        color: #6b7280;
    }
    
    .sidebar-list-container {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    .tab-content {
        display: none;
        height: 100%;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .sidebar-list {
        padding: 8px;
    }
    
    .sidebar-list-item {
        display: flex;
        align-items: center;
        padding: 12px;
        margin-bottom: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .sidebar-list-item:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-list-item.active {
        background: #eff6ff;
        border-color: #3b82f6;
    }
    
    .list-item-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        color: #ffffff;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .list-item-content {
        flex: 1;
        min-width: 0;
    }
    
    .list-item-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
        word-break: break-word;
    }
    
    .list-item-subtitle {
        font-size: 12px;
        color: #6b7280;
        word-break: break-word;
    }
    
    .list-item-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }
    
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #9ca3af;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    
    .empty-state p {
        font-size: 14px;
        margin: 0;
    }
    
    /* Map Selection Styles for Evaluasi */
    .map-selection-container {
        padding: 16px;
    }
    
    .map-selection-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .map-selection-title i {
        font-size: 20px;
        color: #3b82f6;
    }
    
    .map-selection-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .map-selection-item {
        position: relative;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        border: 2px solid #e5e7eb;
        background: #ffffff;
    }
    
    .map-selection-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }
    
    .map-selection-item.selected {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
        background: #eff6ff;
    }
    
    .map-selection-item.selected::after {
        content: '';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #3b82f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
    }
    
    .map-selection-item.selected::before {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        color: #ffffff;
        font-size: 14px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 11;
    }
    
    .map-thumbnail {
        width: 100%;
        height: 100px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .map-thumbnail.map-1 {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .map-thumbnail.map-2 {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .map-thumbnail.map-3 {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    
    .map-thumbnail.map-4 {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .map-thumbnail.map-5 {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
    }
    
    .map-thumbnail.map-6 {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    
    .map-thumbnail-pattern {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0.3;
        background-image: 
            repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px),
            repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);
    }
    
    .map-thumbnail-icon {
        position: relative;
        z-index: 1;
        font-size: 32px;
        color: rgba(255, 255, 255, 0.9);
    }
    
    .map-selection-label {
        padding: 10px 12px;
        text-align: center;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
    }
    
    .map-selection-item.selected .map-selection-label {
        color: #3b82f6;
        font-weight: 600;
        background: #eff6ff;
    }
    
    .map-selection-description {
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
        line-height: 1.4;
    }
    
    .map-selection-info {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-top: 16px;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.6;
    }
    
    .map-selection-info i {
        font-size: 16px;
        color: #3b82f6;
        margin-right: 6px;
        vertical-align: middle;
    }
    
    /* Control Room specific styles */
    .sidebar-list-item[data-type="controlroom"] {
        flex-direction: column;
        align-items: stretch;
        padding: 0;
        overflow: hidden;
        position: relative;
        border-radius: 8px;
        margin-bottom: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    
    .sidebar-list-item[data-type="controlroom"]:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    
    .sidebar-list-item[data-type="controlroom"] .sidebar-list-item-header {
        padding: 14px 16px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: flex;
        align-items: center;
    }
    
    .sidebar-list-item[data-type="controlroom"] .sidebar-list-item-header:hover {
        background-color: #f9fafb;
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded .sidebar-list-item-header {
        background-color: #f0f9ff;
        border-bottom: 1px solid #e0f2fe;
    }
    
    .sidebar-list-item[data-type="controlroom"]:hover .list-item-expand-icon {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded .list-item-expand-icon {
        transform: rotate(180deg);
        background-color: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }
    
    .controlroom-cctv-list {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease;
        background-color: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }
    
    .sidebar-list-item[data-type="controlroom"].expanded .controlroom-cctv-list {
        max-height: 600px;
        padding: 8px 0;
        overflow-y: auto;
    }
    
    .controlroom-cctv-item {
        padding: 10px 16px 10px 48px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        background-color: transparent;
    }
    
    .controlroom-cctv-item:last-child {
        border-bottom: none;
    }
    
    .controlroom-cctv-item:hover {
        background-color: #ffffff;
        padding-left: 52px;
    }
    
    .controlroom-cctv-item.active {
        background-color: #e0f2fe;
        border-left: 3px solid #3b82f6;
        padding-left: 49px;
    }
    
    .controlroom-cctv-item::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #d1d5db;
        transition: all 0.2s ease;
    }
    
    .controlroom-cctv-item:hover::before {
        background-color: #3b82f6;
        width: 8px;
        height: 8px;
    }
    
    .controlroom-cctv-item.active::before {
        background-color: #3b82f6;
        width: 8px;
        height: 8px;
    }
    
    .list-item-expand-icon {
        margin-left: auto;
        color: #6b7280;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.2s ease, color 0.2s ease;
        flex-shrink: 0;
        font-size: 20px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }
    
    /* Collapsed state styles */
    .map-sidebar.collapsed {
        width: 50px;
    }
    
    .map-sidebar.collapsed .sidebar-content {
        width: 50px;
    }
    
    .map-sidebar.collapsed .sidebar-tabs {
        flex-direction: column;
        padding: 8px 4px;
        gap: 4px;
        overflow-x: hidden;
        overflow-y: auto;
    }
    
    .map-sidebar.collapsed .sidebar-tab {
        min-height: 50px;
        padding: 8px 4px;
        width: 100%;
        min-width: auto;
        max-width: none;
    }
    
    .map-sidebar.collapsed .sidebar-tab .tab-label,
    .map-sidebar.collapsed .sidebar-tab .tab-count {
        display: none;
    }
    
    .map-sidebar.collapsed .sidebar-tab i {
        margin-bottom: 0;
    }
    
    .map-sidebar.collapsed .sidebar-body {
        display: none;
    }
    
    .map-sidebar.collapsed .sidebar-toggle-btn {
        left: -40px;
    }
    
    .hazard-detection-header {
        margin-bottom: 24px;
    }

    .hazard-detection-title {
        font-size: 24px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
    }

    .hazard-detection-subtitle {
        font-size: 14px;
        color: #6b7280;
    }

    /* Detail Total CCTV modal styles */
    #totalCctvModal .modal-content {
        background-color: #F8FAFC;
    }

    #totalCctvModal .modal-body {
        background-color: #F8FAFC;
    }

    #totalCctvModal .card {
        background-color: #ffffff;
        border: none;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    #totalCctvModal .card .card-body {
        background-color: #ffffff;
    }

    /* Custom column for 5 items */
    @media (min-width: 992px) {
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }

    .stats-card {
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .stats-card.total {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .stats-card.active {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .stats-card.resolved {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .stats-card.critical {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    .stats-number {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stats-label {
        font-size: 14px;
        opacity: 0.9;
    }

    /* Animation untuk angka yang berubah */
    #modalJumlahAreaKritis,
    #modalCctvAreaKritis,
    #modalCctvAreaNonKritis {
        transition: transform 0.3s ease, color 0.3s ease;
    }

    #modalJumlahAreaKritis.animating,
    #modalCctvAreaKritis.animating,
    #modalCctvAreaNonKritis.animating {
        transform: scale(1.1);
        color: #3b82f6;
    }

    /* Animation untuk badge persentase */
    #detailPersentaseCctvAreaKritis,
    #detailPersentaseCctvAreaNonKritis {
        transition: transform 0.2s ease;
    }

    /* Popup style untuk OpenLayers - diperlukan untuk map */
    .ol-popup {
        position: absolute;
        background-color: white;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #cccccc;
        bottom: 12px;
        left: -50px;
        min-width: 200px;
    }

    .ol-popup:after, .ol-popup:before {
        top: 100%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    .ol-popup:after {
        border-top-color: white;
        border-width: 10px;
        left: 48px;
        margin-left: -10px;
    }

    .ol-popup:before {
        border-top-color: #cccccc;
        border-width: 11px;
        left: 48px;
        margin-left: -11px;
    }

    .ol-popup-closer {
        text-decoration: none;
        position: absolute;
        top: 2px;
        right: 8px;
        color: #333;
        font-size: 18px;
        font-weight: bold;
    }

    .ol-popup-closer:hover {
        color: #000;
    }
    
    /* Map container - menggunakan class dari template */
    #hazardMap {
        width: 100%;
        height: 600px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
    }
    
    /* Map container responsive */
    @media (max-width: 1200px) {
        #hazardMap {
            height: 500px;
        }
    }
    
    @media (max-width: 768px) {
        #hazardMap {
            height: 400px;
        }
    }
    
    /* Hazard item interaction - menggunakan class Bootstrap */
    .hazard-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .hazard-item:hover {
        background-color: #f9fafb;
    }
    
    .hazard-item.selected {
        background-color: #eff6ff;
        border-left: 3px solid #3b82f6;
        padding-left: 12px;
    }

    .hazard-card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        padding: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
    }

    .hazard-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 18px;
        padding: 1px;
        background: linear-gradient(135deg, rgba(14,165,233,.4), rgba(59,130,246,.2));
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }

    .hazard-card-critical::after {
        background: linear-gradient(135deg, rgba(239,68,68,.5), rgba(249,115,22,.4));
    }

    .hazard-card-high::after {
        background: linear-gradient(135deg, rgba(249,115,22,.45), rgba(251,191,36,.35));
    }

    .hazard-card-medium::after {
        background: linear-gradient(135deg, rgba(59,130,246,.4), rgba(14,165,233,.3));
    }

    .hazard-card-low::after {
        background: linear-gradient(135deg, rgba(34,197,94,.45), rgba(16,185,129,.35));
    }

    .hazard-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .hazard-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .hazard-pill-critical {
        background: rgba(239,68,68,.12);
        color: #b91c1c;
    }

    .hazard-pill-high {
        background: rgba(251,191,36,.18);
        color: #b45309;
    }

    .hazard-pill-medium {
        background: rgba(59,130,246,.15);
        color: #1d4ed8;
    }

    .hazard-pill-low {
        background: rgba(16,185,129,.16);
        color: #047857;
    }

    .hazard-card-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-top: 12px;
    }

    .hazard-card-meta span {
        display: block;
        font-size: 12px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .hazard-card-meta strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        margin-top: 4px;
    }

    .hazard-card-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 18px;
    }

    .hazard-chip {
        background: rgba(15,23,42,.04);
        color: #0f172a;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
    }

    .hazard-card-actions .btn {
        border-radius: 999px;
        padding: 6px 16px;
    }

    /* Area Kritis Card Styles */
    .area-kritis-card {
        transition: all 0.2s ease;
    }

    .area-kritis-card:hover {
        border-color: #d1d5db !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .area-icon-wrapper {
        transition: background-color 0.2s ease;
    }

    .area-kritis-card-1:hover .area-icon-wrapper {
        background: #fee2e2 !important;
    }

    .area-kritis-card-2:hover .area-icon-wrapper {
        background: #ffedd5 !important;
    }

    .area-kritis-card-3:hover .area-icon-wrapper {
        background: #dcfce7 !important;
    }

    .luasan-info-card {
        border-radius: 14px;
        border: 1px solid rgba(59, 130, 246, 0.15);
        background: rgba(219, 234, 254, 0.6);
        padding: 14px;
    }
    
    /* CCTV Icon Style */
    .cctv-icon-marker {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 3px solid #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .cctv-icon-marker:hover {
        transform: rotate(-45deg) scale(1.1);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.5);
    }
    
    .cctv-icon-marker::before {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background: #ffffff;
        border-radius: 50%;
        transform: rotate(45deg);
    }
    
    .cctv-icon-marker::after {
        content: '📹';
        position: absolute;
        transform: rotate(45deg);
        font-size: 16px;
        z-index: 1;
    }

    /* Notification Styles */
    .notification-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 400px;
    }

    .notification-item {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 16px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        animation: slideInRight 0.3s ease-out;
        border-left: 4px solid #ef4444;
        position: relative;
        overflow: hidden;
    }

    .notification-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ef4444, #f97316);
        animation: progressBar 5s linear forwards;
    }

    .notification-item.success {
        border-left-color: #10b981;
    }

    .notification-item.success::before {
        background: linear-gradient(90deg, #10b981, #34d399);
    }

    .notification-item.warning {
        border-left-color: #f59e0b;
    }

    .notification-item.warning::before {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }

    .notification-item.info {
        border-left-color: #3b82f6;
    }

    .notification-item.info::before {
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
    }

    .notification-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .notification-item.success .notification-icon {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .notification-item.warning .notification-icon {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .notification-item.info .notification-icon {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        font-size: 14px;
        color: #111827;
        margin-bottom: 4px;
    }

    .notification-message {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.4;
    }

    .notification-time {
        font-size: 11px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .notification-close {
        position: absolute;
        top: 8px;
        right: 8px;
        background: transparent;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .notification-close:hover {
        background: rgba(0, 0, 0, 0.05);
        color: #111827;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes progressBar {
        from {
            width: 100%;
        }
        to {
            width: 0%;
        }
    }

    .notification-item.hiding {
        animation: slideOutRight 0.3s ease-out forwards;
    }
    /* Modal Detail & Intervensi: tampil full dengan Bootstrap standar */
    #detailDopmModal .modal-dialog,
    #intervensiDopmModal .modal-dialog {
        max-width: 90%;
        margin: 0.5rem auto;
    }
    #detailDopmModal .modal-content,
    #intervensiDopmModal .modal-content {
        min-height: 85vh;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    #detailDopmModal .modal-body,
    #intervensiDopmModal .modal-body {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 400px;
    }
    #detailDopmModal .modal-content { background: #fff; }
    #detailDopmModal .modal-header { background: #fff; color: #111827; border-bottom: 1px solid #e5e7eb; }
    #detailDopmModal .modal-body { background: #fff; }
    #detailDopmModal .modal-stat-cards { background: #fff; border-bottom: 1px solid #e5e7eb; }
    #detailDopmModal .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; }
    #detailDopmModal #ipkIkkTableWrap.d-none,
    #detailDopmModal #okkTableWrap.d-none,
    #detailDopmModal #oakTableWrap.d-none { display: none !important; }
    #detailDopmModal #ipkIkkTableWrap:not(.d-none),
    #detailDopmModal #okkTableWrap:not(.d-none),
    #detailDopmModal #oakTableWrap:not(.d-none) { display: block !important; }
    #detailDopmModal #ipkIkkLoading.d-none,
    #detailDopmModal #okkLoading.d-none,
    #detailDopmModal #oakLoading.d-none { display: none !important; }
    #detailDopmModal #ipkIkkEmpty:not(.d-none),
    #detailDopmModal #okkEmpty:not(.d-none),
    #detailDopmModal #oakEmpty:not(.d-none) { display: block !important; }
    #detailDopmModal #ipkIkkEmpty.d-none,
    #detailDopmModal #okkEmpty.d-none,
    #detailDopmModal #oakEmpty.d-none { display: none !important; }
    #detailDopmModal #tableIpkIkk,
    #detailDopmModal #tableOkk,
    #detailDopmModal #tableOak { width: 100%; background: #fff; }
    #intervensiDopmModal .intervensi-section { display: block !important; min-height: 120px; }
    #intervensiDopmModal .intervensi-section .card { margin-bottom: 1rem; }
    #intervensiDopmModal #intervensiIpkLoading:not(.d-none),
    #intervensiDopmModal #intervensiOkkLoading:not(.d-none),
    #intervensiDopmModal #intervensiOakLoading:not(.d-none) { display: block !important; }
    #intervensiDopmModal #intervensiIpkEmpty:not(.d-none),
    #intervensiDopmModal #intervensiOkkEmpty:not(.d-none),
    #intervensiDopmModal #intervensiOakEmpty:not(.d-none) { display: block !important; }
    #intervensiDopmModal #intervensiIpkTableWrap:not(.d-none),
    #intervensiDopmModal #intervensiOkkTableWrap:not(.d-none),
    #intervensiDopmModal #intervensiOakTableWrap:not(.d-none) { display: block !important; }
    .dopm-matriks-row.hover-border:hover { border-color: rgba(0,0,0,0.1) !important; background: rgba(0,0,0,0.02); }
    .dopm-matriks-row.cursor-pointer { cursor: pointer; }
    #tableDopmHarian thead th { white-space: nowrap; }
    #tableIkkClickhouseHarian thead th { white-space: nowrap; }
    #tableIkkClickhouseHarian tbody tr.ikk-row-clickable { cursor: pointer; }

    /* Summary harian per site */
    .dopm-summary-card { border-radius: 1rem; overflow: hidden; border: 1px solid #e5e7eb; }
    .dopm-summary-card .card-header { font-weight: 600; padding: 0.875rem 1rem; border-bottom: 1px solid #e5e7eb; }
    .dopm-summary-table { font-size: 0.8125rem; }
    .dopm-summary-table thead th { white-space: nowrap; font-weight: 600; padding: 0.5rem 0.75rem; }
    .dopm-summary-table tbody td { padding: 0.5rem 0.75rem; vertical-align: middle; }
    .dopm-summary-table tbody tr:hover { background-color: #f8fafc; }
    .dopm-summary-total { font-weight: 700; background-color: #f1f5f9 !important; }
    .dopm-summary-badge-hijau { background-color: #dcfce7; color: #166534; font-weight: 600; }
    .dopm-summary-badge-kuning { background-color: #fef9c3; color: #854d0e; font-weight: 600; }
    .dopm-summary-badge-merah { background-color: #fee2e2; color: #991b1b; font-weight: 600; }
    .dopm-summary-jenis-col { max-width: 10rem; overflow: hidden; text-overflow: ellipsis; }

    /* Scroll area kartu matriks: tinggi tetap, scrollbar disembunyikan */
    .dopm-matriks-list-scroll {
        max-height: 320px;
        overflow-y: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .dopm-matriks-list-scroll::-webkit-scrollbar {
        display: none;
    }
    
    .cctv-icon-marker.live::before {
        background: #10b981;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    /* Animation for Total CCTV Count */
    #totalCctvCountDynamic {
        transition: all 0.3s ease;
    }
    
    #totalCctvCountDynamic.updating {
        opacity: 0.6;
        transform: scale(0.95);
    }
    
    #totalCctvCountDynamic.pulse-animation {
        animation: pulseCctv 0.6s ease-in-out;
    }
    
    @keyframes pulseCctv {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
            color: #10b981;
        }
        100% {
            transform: scale(1);
        }
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@8.2.0/ol.css">
<link rel="stylesheet" href="{{ URL::asset('build/plugins/datatable/css/dataTables.bootstrap5.min.css') }}">
@endsection

@section('content')
<div class="hazard-detection-header">
    <h1 class="hazard-detection-title">DOPM & IKK - Dashboard Weekly</h1>
    <p class="hazard-detection-subtitle">Statistik harian DOPM, IPK-IKK, OKK, dan OAK (Observasi Area Kerja)</p>

    {{-- Filter Week Calendar --}}
    <div class="card rounded-4 mb-3 w-100">
    <div class="card-body py-3">
        <form method="get" action="{{ route('dopmikk.dopm.dashboard-weekly') }}" id="dashboardFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-lg-4">
                    <label class="form-label mb-2 small fw-semibold text-muted">
                        <i class="material-icons-outlined me-1" style="font-size: 16px; vertical-align: middle;">date_range</i>
                        Pilih Week
                    </label>
                    {{-- Bootstrap Week Picker with Input Field --}}
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="material-icons-outlined text-muted" style="font-size: 20px;">calendar_today</i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 rounded-end-3 week-picker-input" 
                               id="weekPickerInput" 
                               readonly 
                               placeholder="Pilih Week..."
                               value="Week {{ $weekNumber ?? '-' }}: {{ $weekStartDate ?? '-' }} - {{ $weekEndDate ?? '-' }}"
                               style="cursor: pointer;">
                    </div>
                    {{-- Hidden datepicker container --}}
                    <div id="weekPickerContainer" class="week-picker-dropdown d-none">
                        <div class="week-picker-calendar"></div>
                    </div>
                    <input type="hidden" name="week" id="filterWeek" value="{{ $filterWeek ?? '' }}">
                    <input type="hidden" name="date" id="filterDate" value="{{ $filterDate ?? now()->toDateString() }}">
                </div>
                <div class="col-12 col-md-4 col-lg-4">
                    <label for="filterSite" class="form-label mb-2 small fw-semibold text-muted">
                        <i class="material-icons-outlined me-1" style="font-size: 16px; vertical-align: middle;">place</i>
                        Site
                    </label>
                    <select name="site" id="filterSite" class="form-select rounded-3">
                        <option value="" {{ ($filterSite ?? '') === '' ? 'selected' : '' }}>Semua Site</option>
                        @php
                            $staticSites = ['BMO 1', 'BMO 2', 'GMO', 'LMO', 'SMO', 'BMO 3','MARINE'];
                        @endphp
                        @foreach($staticSites as $site)
                            <option value="{{ $site }}" {{ ($filterSite ?? '') === $site ? 'selected' : '' }}>{{ $site }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <button type="submit" class="btn btn-primary rounded-3 px-4" id="dashboardFilterBtn">
                        <i class="material-icons-outlined me-1" style="font-size: 18px; vertical-align: middle;">search</i> 
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Bootstrap Week Picker Styles */
.week-picker-input {
    background-color: #fff !important;
}
.week-picker-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    border-color: #3b82f6;
}
.week-picker-dropdown {
    position: absolute;
    z-index: 1050;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    padding: 16px;
    margin-top: 4px;
    min-width: 320px;
}
.week-picker-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
}
.week-picker-header .btn-nav {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    transition: all 0.2s;
}
.week-picker-header .btn-nav:hover {
    background: #f3f4f6;
    color: #111827;
}
.week-picker-header .month-year {
    font-weight: 600;
    font-size: 15px;
    color: #111827;
}
.week-picker-table {
    width: 100%;
    border-collapse: collapse;
}
.week-picker-table th {
    text-align: center;
    font-size: 11px;
    font-weight: 600;
    color: #9ca3af;
    padding: 8px 4px;
    text-transform: uppercase;
}
.week-picker-table td {
    text-align: center;
    font-size: 13px;
    padding: 0;
}
.week-picker-table .week-row {
    cursor: pointer;
    transition: all 0.15s ease;
}
.week-picker-table .week-row:hover td {
    background: #eff6ff;
}
.week-picker-table .week-row.selected td {
    background: #3b82f6;
    color: #fff;
}
.week-picker-table .week-row.selected td:first-child {
    border-radius: 8px 0 0 8px;
}
.week-picker-table .week-row.selected td:last-child {
    border-radius: 0 8px 8px 0;
}
.week-picker-table .week-row td {
    padding: 10px 4px;
}
.week-picker-table .week-num {
    font-weight: 700;
    color: #3b82f6;
    background: #eff6ff;
    border-radius: 6px;
    min-width: 32px;
}
.week-picker-table .week-row.selected .week-num {
    background: #1d4ed8;
    color: #fff;
}
.week-picker-table .other-month {
    color: #d1d5db;
}
.week-picker-table .week-row.selected .other-month {
    color: rgba(255, 255, 255, 0.7);
}
.week-picker-table .today {
    font-weight: 700;
    color: #dc2626;
}
.week-picker-table .week-row.selected .today {
    color: #fef2f2;
}
.week-picker-table .week-row:hover .week-num {
    background: #3b82f6;
    color: #fff;
}
</style>
    
</div>

    {{-- <div class="mb-3">
        <button class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardStatsCollapse" aria-expanded="true" aria-controls="dashboardStatsCollapse">
            <span class="fw-bold d-flex align-items-center">
                <i class="material-icons-outlined me-2">dashboard</i>
                Statistik DOPM, IKK, OKK & OAK
            </span>
            <i class="material-icons-outlined collapse-icon">expand_less</i>
        </button>
    </div> --}}
    
        <div class="row">
         
          <div class="col-12 col-xl-12 d-flex">
            <div class="card rounded-4 w-100">
              <div class="card-body">
                {{-- Header Week Info --}}
                <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                  <i class="material-icons-outlined text-primary">date_range</i>
                  <div>
                    <h6 class="mb-0 fw-bold text-primary">Week {{ $weekNumber ?? '-' }} ({{ $weekYear ?? now()->year }})</h6>
                    <small class="text-muted">{{ $weekStartDate ?? '-' }} - {{ $weekEndDate ?? '-' }}</small>
                  </div>
                </div>
                <div class="d-flex align-items-center justify-content-around flex-wrap gap-4 p-4">
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">assignment</i>
                    </a>
                    <h3 class="mb-0">{{ number_format($totalIkkWeekly ?? 0) }}</h3>
                    <p class="mb-0">Total IKK</p>
                    <small class="text-muted">Week {{ $weekNumber ?? '-' }}</small>
                  </div>
                  <div class="vr"></div>
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">checklist</i>
                    </a>
                    <h3 class="mb-0">{{ $pctIkkAdaIpkWeekly ?? 0 }}%</h3>
                    <p class="mb-0">%IPK Harian</p>
                    <small class="text-muted">Week {{ $weekNumber ?? '-' }}</small>
                  </div>
                  <div class="vr"></div>
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">folder_open</i>
                    </a>
                    <h3 class="mb-0">{{ $pctIkkAdaOkkWeekly ?? 0 }}%</h3>
                    <p class="mb-0">%OKK Harian</p>
                    <small class="text-muted">Week {{ $weekNumber ?? '-' }}</small>
                  </div>
                  <div class="vr"></div>
                  <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                    <a href="javascript:;" class="mb-2 wh-48 bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                      <i class="material-icons-outlined">trending_up</i>
                    </a>
                    <h3 class="mb-0">{{ $pctComplianceWeekly ?? 0 }}%</h3>
                    <p class="mb-0">Compliance</p>
                    <small class="text-muted">Week {{ $weekNumber ?? '-' }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div><!--end row-->
        
        <div class="row">
          <div class="col-12 col-xl-5 col-xxl-4 d-flex">
            <div class="card rounded-4 w-100 shadow-none bg-transparent border-0">
               <div class="card-body p-0">
                 <div class="row g-4">
                    <div class="col-12 col-xl-6 d-flex">
                      <div class="card mb-0 rounded-4 w-100">
                       <div class="card-body">
                         <div class="mb-2">
                           <h5 class="mb-0 fw-bold">IKK Tidak Ada IPK</h5>
                           <p class="mb-0 text-muted small">IKK Tidak Ada IPK</p>
                         </div>
                         <div class="text-center py-3 mt-4">
                           <h1 class="mb-0 display-5 fw-bold">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaIpkCount ?? 0) }}</h1>
                         </div>
                         <div class="text-center mt-3">
                           <p class="mb-0"><span class="text-success me-1">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaIpkCount ?? 0) }}</span> Need Verification pada hari ini</p>
                         </div>
                       </div>
                      </div>
                   </div>
                   <div class="col-12 col-xl-6 d-flex">
                    <div class="card mb-0 rounded-4 w-100">
                     <div class="card-body">
                       <div class="mb-2">
                         <h5 class="mb-0 fw-bold">Pekerjaan Cancel</h5>
                         <p class="mb-0 text-muted small">Total IPK-IKK Status Cancel Weekly</p>
                       </div>
                       <div class="text-center py-3 mt-4">
                         <h1 class="mb-0 display-5 fw-bold">{{ number_format($totalPekerjaanBatalHarian ?? 0) }} Cancel</h1>
                       </div>
                       <div class="text-center mt-3">
                         <p class="mb-0 text-muted small">Data dari IPK-IKK</p>
                       </div>
                     </div>
                    </div>
                 </div>
                   <div class="col-12 col-xl-12">
                    <div class="card rounded-4 mb-0">
                      <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-2">
                           <div class="">
                             @php
                               $pctPengisianRataRataIkk = $pctPengisianRataRataIkk ?? round((($pctIkkAdaIpk ?? 0) + ($pctIkkAdaOkk ?? 0)) / 2, 1);
                             @endphp
                             <h2 class="mb-0">{{ $pctPengisianRataRataIkk }}% Compliance</h2>
                           </div>
                           <div class="">
                             <p class="dash-lable d-flex align-items-center gap-1 rounded mb-0 bg-primary bg-opacity-10 text-primary"><span class="material-icons-outlined fs-6">trending_up</span>Rata-rata IKK</p>
                           </div>
                         </div>
                         <p class="mb-0">Presentase Pengisian IKK (IPK & OKK)</p>
                         <p class="mb-0 small text-muted">Berdasarkan IKK unik: IPK {{ $pctIkkAdaIpk ?? 0 }}% · OKK {{ $pctIkkAdaOkk ?? 0 }}%</p>
                          <div class="mt-4">
                            <p class="mb-2 d-flex align-items-center justify-content-between">Gabungan IPK + OKK (IKK) <span class="">{{ $pctPengisianRataRataIkk }}%</span></p>
                            <div class="progress w-100" style="height: 7px;">
                              <div class="progress-bar bg-primary" style="width: {{ min(100, $pctPengisianRataRataIkk) }}%"></div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>

                 </div><!--end row-->
               </div>
            </div>  
          </div> 
          <div class="col-12 col-xl-7 col-xxl-8 d-flex">
            <div class="card w-100 rounded-4">
               <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Summary Matriks Evaluasi — Kalender Compliance IKK</h5>
                  </div>
                 </div>
                @php
                  $pctCalendar = $pctPengisianRataRataIkk ?? round((($pctIkkAdaIpk ?? 0) + ($pctIkkAdaOkk ?? 0)) / 2, 1);
                @endphp
                <style>
                  .compliance-calendar-wrapper { background: rgba(255,255,255,0.03); border-radius: 15px; padding: 20px; border: 1px solid rgba(0,0,0,0.06); }
                  .compliance-calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: rgba(0,0,0,0.04); border-radius: 10px; }
                  .compliance-calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; margin-top: 20px; }
                  .compliance-day-header { text-align: center; font-weight: 600; padding: 12px 8px; background: rgba(0,0,0,0.06); border-radius: 8px; font-size: 0.85rem; }
                  .compliance-day-cell { aspect-ratio: 1; padding: 10px; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; min-height: 100px; display: flex; flex-direction: column; justify-content: space-between; }
                  .compliance-day-cell:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
                  .compliance-day-cell.positive { background: linear-gradient(135deg, #00c853 0%, #00e676 100%); border: 2px solid #00ff7f; color: #fff; }
                  .compliance-day-cell.negative { background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%); border: 2px solid #ff1744; color: #fff; }
                  .compliance-day-cell.neutral { background: linear-gradient(135deg, #ff6f00 0%, #ff9800 100%); border: 2px solid #ffa726; color: #fff; }
                  .compliance-day-cell.empty { background: rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.08); color: #6c757d; }
                  .compliance-day-cell.empty:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
                  .compliance-day-number { font-size: 1rem; font-weight: 700; }
                  .compliance-day-value { margin-top: auto; font-size: 1.1rem; font-weight: 700; }
                  .compliance-day-label { font-size: 0.7rem; opacity: 0.9; }
                  .compliance-legend { display: flex; gap: 20px; justify-content: center; margin-top: 20px; padding: 15px; background: rgba(0,0,0,0.04); border-radius: 10px; flex-wrap: wrap; }
                  .compliance-legend-item { display: flex; align-items: center; gap: 8px; }
                  .compliance-legend-color { width: 28px; height: 28px; border-radius: 6px; }
                  .btn-month-nav { background: rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.1); color: #374151; padding: 8px 16px; border-radius: 8px; transition: all 0.3s; }
                  .btn-month-nav:hover { background: rgba(0,0,0,0.1); color: #111; }
                  @media (max-width: 768px) { .compliance-calendar-grid { gap: 6px; } .compliance-day-cell { min-height: 80px; padding: 6px; } .compliance-day-value { font-size: 0.95rem; } }
                </style>
                <div class="compliance-calendar-wrapper">
                  <div class="compliance-calendar-header">
                    <button type="button" class="btn btn-month-nav" id="compliancePrevMonth"><i class="material-icons-outlined" style="font-size:18px;vertical-align:middle">chevron_left</i> Bulan Sebelumnya</button>
                    <h5 class="mb-0" id="complianceCurrentMonth"></h5>
                    <button type="button" class="btn btn-month-nav" id="complianceNextMonth">Bulan Berikutnya <i class="material-icons-outlined" style="font-size:18px;vertical-align:middle">chevron_right</i></button>
                  </div>
                  <div class="compliance-calendar-grid">
                    <div class="compliance-day-header">Minggu</div>
                    <div class="compliance-day-header">Senin</div>
                    <div class="compliance-day-header">Selasa</div>
                    <div class="compliance-day-header">Rabu</div>
                    <div class="compliance-day-header">Kamis</div>
                    <div class="compliance-day-header">Jumat</div>
                    <div class="compliance-day-header">Sabtu</div>
                  </div>
                  <div class="compliance-calendar-grid" id="complianceCalendarDays"></div>
                  <div class="compliance-legend">
                    <div class="compliance-legend-item">
                      <div class="compliance-legend-color" style="background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);"></div>
                      <span class="small">Merah (1–50%)</span>
                    </div>
                    <div class="compliance-legend-item">
                      <div class="compliance-legend-color" style="background: linear-gradient(135deg, #ff6f00 0%, #ff9800 100%);"></div>
                      <span class="small">Kuning (51–80%)</span>
                    </div>
                    <div class="compliance-legend-item">
                      <div class="compliance-legend-color" style="background: linear-gradient(135deg, #00c853 0%, #00e676 100%);"></div>
                      <span class="small">Hijau (81–100%)</span>
                    </div>
                  </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 border p-3 rounded-4 mt-3 text-start">
                  <span class="small text-muted">
                    Compliance = rata-rata pengisian IKK (IPK & OKK) per hari. Klik tanggal untuk memuat data hari tersebut. Merah 1–50%, Kuning 51–80%, Hijau 81–100%.
                  </span>
                </div>
               </div>
            </div>
          </div> 
        </div><!--end row-->

        <div class="row mt-3">
           <div class="col-12 col-xl-4 d-flex">
            <div class="card w-100 rounded-4">
               <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Need Action</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>
                  <div class="d-flex flex-column gap-4 dopm-matriks-list-scroll">
                  @php
                        // Gunakan data IKK (work permit) dari ClickHouse untuk Need Action (Merah)
                        $ikkMerah = collect($ikkClickhouseListHarian ?? [])->where('status_matriks', 'Merah')->values();
                    @endphp
                    @forelse($ikkMerah as $ikk)
                     @php
                        // Data dari IKK/work permit (bukan DOPM)
                        $dopmJson = [
                            'kode_ikk' => $ikk->code ?? null,
                            'work_permit_id' => $ikk->id ?? null,
                            'jenis_ijin_kerja_khusus' => $ikk->jenis_ijin_kerja_khusus ?? null,
                            'sid_layer_2' => $ikk->sid_layer_2 ?? null,
                            'sid_layer_3' => $ikk->sid_layer_3 ?? null,
                            'sid_layer_4' => $ikk->sid_layer_4 ?? null,
                            'nama_layer_2' => $ikk->nama_layer_2 ?? null,
                            'nama_layer_3' => $ikk->nama_layer_3 ?? null,
                            'nama_layer_4' => $ikk->nama_layer_4 ?? null,
                            'nama_layer_1' => $ikk->nama_layer_1 ?? null,
                            'sid_layer_1' => $ikk->sid_layer_1 ?? null,
                            'id_dop' => $ikk->code ?? null,
                            'nama_pekerjaan' => $ikk->nama_pekerjaan ?? null,
                            'site_ijin_kerja_khusus' => $ikk->site ?? null,
                            'perusahaan_ijin_kerja_khusus' => $ikk->perusahaan ?? null,
                            'tanggal_dop' => $filterDate ?? null,
                            'timestamp' => null,
                            'status' => $ikk->status ?? null,
                            'location_name' => $ikk->location_name ?? null,
                            'location_detail_name' => $ikk->location_detail_name ?? null,
                            'ra_pjo_name' => $ikk->ra_pjo_name ?? null,
                        ];
                     @endphp
                     @php
                        $alasanMerah = htmlspecialchars($ikk->alasan_matriks ?? 'Tidak ada IPK atau OKK', ENT_QUOTES, 'UTF-8');
                     @endphp
                     <div class="dopm-matriks-row d-flex align-items-center gap-4 rounded-3 p-2 border border-transparent hover-border cursor-pointer" role="button" tabindex="0" data-dopm="{{ json_encode($dopmJson) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="<strong>Alasan Status Merah:</strong><br>{{ $alasanMerah }}" title="Klik untuk detail DOPM, IPK-IKK, OKK, OAK">
                       <div class="d-flex align-items-center gap-3 flex-grow-1 flex-shrink-0 min-w-0">
                        <div class="wh-48 d-flex align-items-center justify-content-center rounded-3 border bg-danger bg-opacity-10 text-danger flex-shrink-0">
                          <span class="material-icons-outlined" style="font-size: 28px;">warning</span>
                        </div>
                          <div class="min-w-0">
                            <h6 class="mb-0 fw-bold text-truncate" title="{{ $ikk->jenis_ijin_kerja_khusus ?? $ikk->code ?? '-' }}">{{ $ikk->jenis_ijin_kerja_khusus ?? $ikk->code ?? '-' }}</h6>
                            <p class="mb-0 text-muted small text-truncate" title="{{ $ikk->code ?? '-' }} • {{ $ikk->site ?? '-' }}">{{ $ikk->code ?? '-' }} • {{ $ikk->site ?? '-' }}</p>
                          </div>
                       </div>
                       <div class="progress w-25 flex-shrink-0" style="height: 5px;">
                          <div class="progress-bar bg-danger" style="width: 0%"></div>
                       </div>
                       <div class="flex-shrink-0 d-flex align-items-center gap-2">
                       
                        <p class="mb-0 fs-6">0%</p>
                       </div>
                     </div>
                    @empty
                     <div class="text-center py-4 text-muted">
                        <span class="material-icons-outlined" style="font-size: 48px;">check_circle</span>
                        <p class="mb-0 mt-2 small">Tidak ada DOPM matriks Merah untuk tanggal ini.</p>
                     </div>
                    @endforelse
                  </div>
               </div>
             </div>
           </div>

           <div class="col-12 col-xl-4 d-flex">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Warning</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>
                <div class="d-flex flex-column gap-4 dopm-matriks-list-scroll">
                  @php
                      // Gunakan data IKK (work permit) dari ClickHouse untuk Warning (Kuning)
                      $ikkKuning = collect($ikkClickhouseListHarian ?? [])->where('status_matriks', 'Kuning')->values();
                  @endphp
                  @forelse($ikkKuning as $ikk)
                  @php
                    // Data dari IKK/work permit (bukan DOPM)
                    $dopmJsonK = [
                        'kode_ikk' => $ikk->code ?? null,
                        'work_permit_id' => $ikk->id ?? null,
                        'jenis_ijin_kerja_khusus' => $ikk->jenis_ijin_kerja_khusus ?? null,
                        'sid_layer_2' => $ikk->sid_layer_2 ?? null,
                        'sid_layer_3' => $ikk->sid_layer_3 ?? null,
                        'sid_layer_4' => $ikk->sid_layer_4 ?? null,
                        'nama_layer_2' => $ikk->nama_layer_2 ?? null,
                        'nama_layer_3' => $ikk->nama_layer_3 ?? null,
                        'nama_layer_4' => $ikk->nama_layer_4 ?? null,
                        'nama_layer_1' => $ikk->nama_layer_1 ?? null,
                        'sid_layer_1' => $ikk->sid_layer_1 ?? null,
                        'id_dop' => $ikk->code ?? null,
                        'nama_pekerjaan' => $ikk->nama_pekerjaan ?? null,
                        'site_ijin_kerja_khusus' => $ikk->site ?? null,
                        'perusahaan_ijin_kerja_khusus' => $ikk->perusahaan ?? null,
                        'tanggal_dop' => $filterDate ?? null,
                        'timestamp' => null,
                        'status' => $ikk->status ?? null,
                        'location_name' => $ikk->location_name ?? null,
                        'location_detail_name' => $ikk->location_detail_name ?? null,
                        'ra_pjo_name' => $ikk->ra_pjo_name ?? null,
                    ];
                  @endphp
                  @php
                    $alasanKuning = htmlspecialchars($ikk->alasan_matriks ?? 'Kondisi tidak memenuhi kriteria Hijau', ENT_QUOTES, 'UTF-8');
                  @endphp
                  <div class="dopm-matriks-row d-flex align-items-center gap-4 rounded-3 p-2 border border-transparent hover-border cursor-pointer" role="button" tabindex="0" data-dopm="{{ json_encode($dopmJsonK) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="<strong>Alasan Status Kuning:</strong><br>{{ $alasanKuning }}" title="Klik untuk detail DOPM, IPK-IKK, OKK, OAK">
                    <div class="d-flex align-items-center gap-3 flex-grow-1 flex-shrink-0 min-w-0">
                      <div class="wh-48 d-flex align-items-center justify-content-center rounded-3 border bg-warning bg-opacity-10 text-warning flex-shrink-0">
                        <span class="material-icons-outlined" style="font-size: 28px;">info</span>
                      </div>
                      <div class="min-w-0">
                        <h6 class="mb-0 fw-bold text-truncate" title="{{ $ikk->jenis_ijin_kerja_khusus ?? $ikk->code ?? '-' }}">{{ $ikk->jenis_ijin_kerja_khusus ?? $ikk->code ?? '-' }}</h6>
                        <p class="mb-0 text-muted small text-truncate" title="{{ $ikk->code ?? '-' }} • {{ $ikk->site ?? '-' }}">{{ $ikk->code ?? '-' }} • {{ $ikk->site ?? '-' }}</p>
                      </div>
                    </div>
                    <div class="progress w-25 flex-shrink-0" style="height: 5px;">
                      <div class="progress-bar bg-warning text-dark" style="width: 50%"></div>
                    </div>
                    <div class="flex-shrink-0 d-flex align-items-center gap-2">
                     
                      <p class="mb-0 fs-6">50%</p>
                    </div>
                  </div>
                  @empty
                  <div class="text-center py-4 text-muted">
                    <span class="material-icons-outlined" style="font-size: 48px;">check_circle</span>
                    <p class="mb-0 mt-2 small">Tidak ada DOPM matriks Kuning untuk tanggal ini.</p>
                  </div>
                  @endforelse
                </div>
              </div>
            </div>  
          </div>

           <div class="col-12 col-xl-4 d-flex">
            <div class="card w-100 rounded-4">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                  <div class="">
                    <h5 class="mb-0 fw-bold">Complete</h5>
                  </div>
                  <div class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle-nocaret options dropdown-toggle"
                      data-bs-toggle="dropdown">
                      <span class="material-icons-outlined fs-5">more_vert</span>
                    </a>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                 </div>
                <div class="d-flex flex-column gap-4 dopm-matriks-list-scroll">
                  @php
                      // Gunakan data IKK (work permit) dari ClickHouse untuk Complete (Hijau)
                      $ikkHijau = collect($ikkClickhouseListHarian ?? [])->where('status_matriks', 'Hijau')->values();
                  @endphp
                  @forelse($ikkHijau as $ikk)
                  @php
                    // Data dari IKK/work permit (bukan DOPM)
                        $dopmJsonH = [
                            'kode_ikk' => $ikk->code ?? null,
                            'work_permit_id' => $ikk->id ?? null,
                            'jenis_ijin_kerja_khusus' => $ikk->jenis_ijin_kerja_khusus ?? null,
                            'sid_layer_2' => $ikk->sid_layer_2 ?? null,
                        'sid_layer_3' => $ikk->sid_layer_3 ?? null,
                        'sid_layer_4' => $ikk->sid_layer_4 ?? null,
                        'nama_layer_2' => $ikk->nama_layer_2 ?? null,
                        'nama_layer_3' => $ikk->nama_layer_3 ?? null,
                        'nama_layer_4' => $ikk->nama_layer_4 ?? null,
                        'nama_layer_1' => $ikk->nama_layer_1 ?? null,
                        'sid_layer_1' => $ikk->sid_layer_1 ?? null,
                        'id_dop' => $ikk->code ?? null,
                        'nama_pekerjaan' => $ikk->nama_pekerjaan ?? null,
                        'site_ijin_kerja_khusus' => $ikk->site ?? null,
                        'perusahaan_ijin_kerja_khusus' => $ikk->perusahaan ?? null,
                        'tanggal_dop' => $filterDate ?? null,
                        'timestamp' => null,
                        'status' => $ikk->status ?? null,
                        'location_name' => $ikk->location_name ?? null,
                        'location_detail_name' => $ikk->location_detail_name ?? null,
                        'ra_pjo_name' => $ikk->ra_pjo_name ?? null,
                    ];
                  @endphp
                  @php
                    $alasanHijau = htmlspecialchars($ikk->alasan_matriks ?? 'Semua persyaratan terpenuhi', ENT_QUOTES, 'UTF-8');
                  @endphp
                  <div class="dopm-matriks-row d-flex align-items-center gap-4 rounded-3 p-2 border border-transparent hover-border cursor-pointer" role="button" tabindex="0" data-dopm="{{ json_encode($dopmJsonH) }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" data-bs-title="<strong>Alasan Status Hijau:</strong><br>{{ $alasanHijau }}" title="Klik untuk detail DOPM, IPK-IKK, OKK, OAK">
                    <div class="d-flex align-items-center gap-3 flex-grow-1 flex-shrink-0 min-w-0">
                      <div class="wh-48 d-flex align-items-center justify-content-center rounded-3 border bg-success bg-opacity-10 text-success flex-shrink-0">
                        <span class="material-icons-outlined" style="font-size: 28px;">check_circle</span>
                      </div>
                      <div class="min-w-0">
                        <h6 class="mb-0 fw-bold text-truncate" title="{{ $ikk->jenis_ijin_kerja_khusus ?? $ikk->code ?? '-' }}">{{ $ikk->jenis_ijin_kerja_khusus ?? $ikk->code ?? '-' }}</h6>
                        <p class="mb-0 text-muted small text-truncate" title="{{ $ikk->code ?? '-' }} • {{ $ikk->site ?? '-' }}">{{ $ikk->code ?? '-' }} • {{ $ikk->site ?? '-' }}</p>
                      </div>
                    </div>
                    <div class="progress w-25 flex-shrink-0" style="height: 5px;">
                      <div class="progress-bar bg-success" style="width: 100%"></div>
                    </div>
                    <div class="flex-shrink-0 d-flex align-items-center gap-2">
                    
                      <p class="mb-0 fs-6">100%</p>
                    </div>
                  </div>
                  @empty
                  <div class="text-center py-4 text-muted">
                    <span class="material-icons-outlined" style="font-size: 48px;">inbox</span>
                    <p class="mb-0 mt-2 small">Tidak ada DOPM matriks Hijau untuk tanggal ini.</p>
                  </div>
                  @endforelse
                </div>
              </div>
            </div>
         </div>

         

    <div class="collapse show" id="dashboardStatsCollapse">
        {{-- <div class="row">
            <div class="col-12 d-flex">
                <div class="card rounded-4 w-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">Ringkasan Data Harian</h5>
                            <span class="badge bg-primary">{{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-around flex-wrap gap-4 p-4">
                            <a href="" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Data DOPM tanggal terpilih">
                                <span class="mb-2 wh-48 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">assignment</i>
                                </span>
                                <h3 class="mb-0">{{ number_format($totalDopmHarian ?? 0) }}</h3>
                                <p class="mb-0">IKK</p>
                                <small class="text-muted">Data Hari ini</small>
                            </a>
                            <div class="vr"></div>
                            <a href="" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Presentase IKK yang ada IPK ({{ $ikkAdaIpkCount ?? 0 }}/{{ $totalIkkUnikHarian ?? 0 }} IKK)">
                                <span class="mb-2 wh-48 bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">checklist</i>
                                </span>
                                <h3 class="mb-0">{{ $pctIkkAdaIpk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada IPK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaIpkCount ?? 0) }} belum IPK-IKK</small>
                            </a>
                            <div class="vr"></div>
                            <a href="" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Presentase IKK yang ada OKK ({{ $ikkAdaOkkCount ?? 0 }}/{{ $totalIkkUnikHarian ?? 0 }} IKK)">
                                <span class="mb-2 wh-48 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">folder_open</i>
                                </span>
                                <h3 class="mb-0">{{ $pctIkkAdaOkk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada OKK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaOkkCount ?? 0) }} belum OKK</small>
                            </a>
                            <div class="vr"></div>
                            <div class="d-flex flex-column align-items-center justify-content-center gap-2" title="OAK (Observasi Area Kerja) dari ClickHouse — tipe OBSERVE, layer 2/3/4 DOPM">
                                <span class="mb-2 wh-48 bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">visibility</i>
                                </span>
                                <h3 class="mb-0">{{ number_format($totalOakHarian ?? 0) }}</h3>
                                <p class="mb-0">OAK</p>
                                <small class="text-muted">Data Hari ini (OBSERVE, Layer 2/3/4)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- Summary harian per site: Jenis IJK & Status Matriks --}}
        {{-- @if(count($summaryBySite ?? []) > 0)
        <div class="row mt-3 g-3">
            <div class="col-12">
                <h5 class="mb-2 fw-bold d-flex align-items-center gap-2">
                    <span class="material-icons-outlined text-primary">summarize</span>
                    Ringkasan Ijin Kerja Khusus per Site — {{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('l, d F Y') }}
                </h5>
            </div>
            <div class="col-12 col-xl-7">
                <div class="card dopm-summary-card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex align-items-center gap-2">
                        <span class="material-icons-outlined text-primary" style="font-size: 1.25rem;">category</span>
                        Jumlah per Jenis IJK per Site
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dopm-summary-table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Site</th>
                                        @foreach($summaryJenisKeys as $jk)
                                            <th class="text-center dopm-summary-jenis-col" title="{{ $jk }}">{{ strlen($jk) > 20 ? substr($jk, 0, 17) . '…' : $jk }}</th>
                                        @endforeach
                                        <th class="text-end fw-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryBySite as $site => $row)
                                        <tr>
                                            <td class="fw-semibold">{{ $site }}</td>
                                            @foreach($summaryJenisKeys as $jk)
                                                @php $cnt = $row['jenis'][$jk] ?? 0; @endphp
                                                <td class="text-center">{{ $cnt > 0 ? $cnt : '—' }}</td>
                                            @endforeach
                                            <td class="text-end dopm-summary-total">{{ $row['total'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-5">
                <div class="card dopm-summary-card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex align-items-center gap-2">
                        <span class="material-icons-outlined text-success" style="font-size: 1.25rem;">pie_chart</span>
                        Status Matriks per Site
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover dopm-summary-table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Site</th>
                                        <th class="text-center" style="background-color: #dcfce7;">Hijau</th>
                                        <th class="text-center" style="background-color: #fef9c3;">Kuning</th>
                                        <th class="text-center" style="background-color: #fee2e2;">Merah</th>
                                        <th class="text-end fw-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summaryBySite as $site => $row)
                                        @php
                                            $total = $row['total'] ?? 0;
                                            $h = $row['hijau'] ?? 0;
                                            $k = $row['kuning'] ?? 0;
                                            $m = $row['merah'] ?? 0;
                                            $pctH = $total > 0 ? round($h / $total * 100, 0) : 0;
                                            $pctK = $total > 0 ? round($k / $total * 100, 0) : 0;
                                            $pctM = $total > 0 ? round($m / $total * 100, 0) : 0;
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $site }}</td>
                                            <td class="text-center dopm-summary-badge-hijau">{{ $pctH }}% <span class="d-block small">({{ $h }})</span></td>
                                            <td class="text-center dopm-summary-badge-kuning">{{ $pctK }}% <span class="d-block small">({{ $k }})</span></td>
                                            <td class="text-center dopm-summary-badge-merah">{{ $pctM }}% <span class="d-block small">({{ $m }})</span></td>
                                            <td class="text-end dopm-summary-total">100% <span class="d-block small">({{ $total }})</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif --}}

        {{-- Data DOPM harian (tampil langsung) --}}
        <div class="row mt-3">
            <div class="col-12 px-3 px-md-4">
                <!-- <div class="card rounded-4 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0 fw-bold">Data DOPM — {{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('l, d F Y') }}</h5>
                        <small class="text-muted">Klik Detail untuk melihat IPK-IKK, OKK, OAK per DOPM</small>
                    </div>
                    <div class="card-body p-0">
                        @if(count($dopmListHarian ?? []) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped align-middle mb-0 w-100" id="tableDopmHarian">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID DOP</th>
                                            <th>Kode IKK</th>
                                            <th>Site</th>
                                            <th>Jenis Ijin Kerja Khusus</th>
                                            <th>Nama Pekerjaan</th>
                                            <th>Perusahaan</th>
                                            <th>Status</th>
                                            <th>Status Matriks</th>
                                            <th>Nama Layer 1</th>
                                            <th>Layer 2 / 3 / 4</th>
                                            <th class="text-end">Intervensi</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dopmListHarian as $dopm)
                                            <tr>
                                                <td>{{ $dopm->id_dop ?? '-' }}</td>
                                                <td>{{ $dopm->kode_ikk ?? '-' }}</td>
                                                <td>{{ $dopm->site_ijin_kerja_khusus ?? '-' }}</td>
                                                <td>{{ $dopm->jenis_ijin_kerja_khusus ?? '-' }}</td>
                                                <td>{{ $dopm->nama_pekerjaan ?? '-' }}</td>
                                                <td>{{ $dopm->perusahaan_ijin_kerja_khusus ?? '-' }}</td>
                                                <td><span class="badge bg-secondary">{{ $dopm->status ?? '-' }}</span></td>
                                                <td>
                                                    @php
                                                        $matriks = $dopm->status_matriks ?? 'Merah';
                                                        $badgeClass = $matriks === 'Hijau' ? 'bg-success' : ($matriks === 'Kuning' ? 'bg-warning text-dark' : 'bg-danger');
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}" title="IKK ada IPK: {{ isset($dopm->is_ikk_ada_ipk) ? ($dopm->is_ikk_ada_ipk ? 'Ya' : 'Tidak') : '-' }}, IKK ada OKK: {{ isset($dopm->is_ikk_ada_okk) ? ($dopm->is_ikk_ada_okk ? 'Ya' : 'Tidak') : '-' }}">{{ $matriks }}</span>
                                                </td>
                                                <td><small class="text-primary">{{ $dopm->nama_layer_1 ?? '-' }}</small></td>
                                                <td><small>{{ $dopm->nama_layer_2 ?? '-' }} / {{ $dopm->nama_layer_3 ?? '-' }} / {{ $dopm->nama_layer_4 ?? '-' }}</small></td>
                                                <td class="text-end">
                                                    @php
                                                        $dopmJson = [
                                                            'kode_ikk' => $dopm->kode_ikk,
                                                            'jenis_ijin_kerja_khusus' => $dopm->jenis_ijin_kerja_khusus,
                                                            'sid_layer_2' => $dopm->sid_layer_2,
                                                            'sid_layer_3' => $dopm->sid_layer_3,
                                                            'sid_layer_4' => $dopm->sid_layer_4,
                                                            'nama_layer_2' => $dopm->nama_layer_2,
                                                            'nama_layer_3' => $dopm->nama_layer_3,
                                                            'nama_layer_4' => $dopm->nama_layer_4,
                                                            'nama_layer_1' => $dopm->nama_layer_1 ?? null,
                                                            'sid_layer_1' => $dopm->sid_layer_1 ?? null,
                                                            'id_dop' => $dopm->id_dop,
                                                            'nama_pekerjaan' => $dopm->nama_pekerjaan,
                                                            'site_ijin_kerja_khusus' => $dopm->site_ijin_kerja_khusus ?? null,
                                                            'perusahaan_ijin_kerja_khusus' => $dopm->perusahaan_ijin_kerja_khusus ?? null,
                                                            'tanggal_dop' => $dopm->tanggal_dop ? (\Carbon\Carbon::parse($dopm->tanggal_dop)->format('Y-m-d')) : null,
                                                            'timestamp' => $dopm->timestamp ? $dopm->timestamp->format('Y-m-d H:i') : null,
                                                            'status' => $dopm->status ?? null,
                                                        ];
                                                    @endphp
                                                    <button type="button" class="btn btn-sm btn-outline-warning btn-intervensi-dopm" data-dopm="{{ json_encode($dopmJson) }}" title="Intervensi (IPK-IKK, OKK, OAK)">
                                                        <i class="material-icons-outlined" style="font-size: 16px;">campaign</i> Intervensi
                                                    </button>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-detail-dopm" data-dopm="{{ json_encode($dopmJson) }}">
                                                        <i class="material-icons-outlined" style="font-size: 16px;">visibility</i> Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                <p class="mb-0 mt-2">Tidak ada data DOPM untuk tanggal ini.</p>
                            </div>
                        @endif
                    </div>
                </div> -->

                {{-- Tabel IKK dari ClickHouse (ikk_work_permit) dengan Expand/Collapse --}}
                <div class="card rounded-4 border-0 shadow-sm mt-2">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-0 fw-bold">
                                Data IKK Weekly 
                                ({{ $weekStartDate ?? '-' }} - {{ $weekEndDate ?? '-' }})
                            </h5>
                            <small class="text-muted">Data IKK Weekly (work permit) yang sudah di-approve KWTT, distinct per kode IKK. Klik tombol [+] untuk melihat detail IPK/OKK per tanggal.</small>
                        </div>
                        <div class="d-flex gap-2">
                            @if(count($ikkClickhouseListHarian ?? []) > 0)
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnExpandAllIkk" title="Expand All">
                                    <i class="material-icons-outlined" style="font-size: 16px;">unfold_more</i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnCollapseAllIkk" title="Collapse All">
                                    <i class="material-icons-outlined" style="font-size: 16px;">unfold_less</i>
                                </button>
                                <a href="{{ route('dopmikk.dopm.dashboard-weekly.export-ikk-excel', ['week' => $filterWeek ?? now()->format('o-\\WW'), 'site' => request('site')]) }}" 
                                   class="btn btn-success btn-sm d-flex align-items-center gap-1" 
                                   title="Download Excel">
                                    <i class="material-icons-outlined" style="font-size: 18px;">download</i>
                                    <span>Download Excel</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(count($ikkClickhouseListHarian ?? []) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 w-100" id="tableIkkClickhouseHarian">
                                   <thead class="table-light">
                                       <tr>
                                           <th style="width: 40px;"></th>
                                           <th>No</th>
                                           <th>Kode IKK</th>
                                           <th>Site</th>
                                           <th>Jenis Ijin</th>
                                           <th>Nama Pekerjaan</th>
                                           <th>Perusahaan</th>
                                           <th>Periode</th>
                                           <th>Status WP</th>
                                           <th>IPK</th>
                                           <th>OKK</th>
                                           <th>PIC Approver</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ikkClickhouseListHarian as $ikk)
                                            @php
                                                $totalHari = $ikk->total_hari ?? 0;
                                                $ipkCount = $ikk->ipk_count ?? 0;
                                                $okkCount = $ikk->okk_count ?? 0;
                                                $dailyDetails = $ikk->daily_details ?? [];
                                                
                                                // Badge class untuk IPK
                                                if ($totalHari > 0 && $ipkCount >= $totalHari) {
                                                    $ipkBadgeClass = 'bg-success';
                                                } elseif ($ipkCount > 0) {
                                                    $ipkBadgeClass = 'bg-warning text-dark';
                                                } else {
                                                    $ipkBadgeClass = 'bg-danger';
                                                }
                                                
                                                // Badge class untuk OKK
                                                if ($totalHari > 0 && $okkCount >= $totalHari) {
                                                    $okkBadgeClass = 'bg-success';
                                                } elseif ($okkCount > 0) {
                                                    $okkBadgeClass = 'bg-warning text-dark';
                                                } else {
                                                    $okkBadgeClass = 'bg-danger';
                                                }
                                                
                                                // Format periode
                                                try {
                                                    $startFormatted = $ikk->start_date ? \Carbon\Carbon::parse($ikk->start_date)->format('d M') : '-';
                                                    $endFormatted = $ikk->end_date ? \Carbon\Carbon::parse($ikk->end_date)->format('d M') : '-';
                                                    $periode = $startFormatted . ' - ' . $endFormatted;
                                                } catch (\Throwable $e) {
                                                    $periode = '-';
                                                }
                                            @endphp
                                            {{-- Main Row --}}
                                            <tr class="ikk-main-row" data-ikk-id="{{ $loop->iteration }}">
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-primary ikk-toggle-btn p-0" 
                                                            data-target="ikk-detail-{{ $loop->iteration }}" 
                                                            style="width: 28px; height: 28px; line-height: 1;">
                                                        <i class="material-icons-outlined" style="font-size: 18px;">add</i>
                                                    </button>
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong class="text-primary">{{ $ikk->code ?? '-' }}</strong></td>
                                                <td>{{ $ikk->site ?? '-' }}</td>
                                                <td><small>{{ \Illuminate\Support\Str::limit($ikk->jenis_ijin_kerja_khusus ?? '-', 20) }}</small></td>
                                                <td><small>{{ \Illuminate\Support\Str::limit($ikk->nama_pekerjaan ?? '-', 25) }}</small></td>
                                                <td><small>{{ \Illuminate\Support\Str::limit($ikk->perusahaan ?? '-', 20) }}</small></td>
                                                <td><small>{{ $periode }}</small></td>
                                                <td><span class="badge bg-secondary">{{ $ikk->status ?? '-' }}</span></td>
                                                <td>
                                                    <span class="badge {{ $ipkBadgeClass }}" title="IPK: {{ $ipkCount }}/{{ $totalHari }} hari">
                                                        {{ $ipkCount }}/{{ $totalHari }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $okkBadgeClass }}" title="OKK: {{ $okkCount }}/{{ $totalHari }} hari">
                                                        {{ $okkCount }}/{{ $totalHari }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(!empty($ikk->pic_approver_name))
                                                        <small class="fw-semibold text-primary" title="{{ $ikk->pic_approver_name }}">
                                                            {{ \Illuminate\Support\Str::limit($ikk->pic_approver_name, 15) }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            {{-- Detail Row (Hidden by default) --}}
                                            <tr class="ikk-detail-row d-none" id="ikk-detail-{{ $loop->iteration }}">
                                                <td colspan="12" class="p-0 bg-light">
                                                    <div class="p-3">
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <small class="text-muted">
                                                                    <strong>Layer 1:</strong> {{ $ikk->nama_layer_1 ?? '-' }}<br>
                                                                    <strong>Layer 2:</strong> {{ $ikk->nama_layer_2 ?? '-' }}<br>
                                                                    <strong>Layer 3:</strong> {{ $ikk->nama_layer_3 ?? '-' }}<br>
                                                                    <strong>Layer 4:</strong> {{ $ikk->nama_layer_4 ?? '-' }}
                                                                </small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <small class="text-muted">
                                                                    <strong>Lokasi:</strong> {{ $ikk->location_name ?? '-' }}<br>
                                                                    <strong>Detail Lokasi:</strong> {{ $ikk->location_detail_name ?? '-' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered mb-0 bg-white">
                                                                <thead class="table-secondary">
                                                                    <tr>
                                                                        <th style="width: 120px;">Tanggal</th>
                                                                        <th style="width: 100px;">Hari</th>
                                                                        <th>IPK</th>
                                                                        <th>Detail IPK</th>
                                                                        <th>OKK</th>
                                                                        <th>Detail OKK</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($dailyDetails as $detail)
                                                                        <tr>
                                                                            <td>{{ $detail['tanggal'] ?? '-' }}</td>
                                                                            <td>{{ $detail['hari'] ?? '-' }}</td>
                                                                            <td class="text-center">
                                                                                @if($detail['has_ipk'] ?? false)
                                                                                    <span class="badge bg-success">Ada</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Tidak</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($detail['has_ipk'] ?? false)
                                                                                    <small>
                                                                                        <strong>Kode:</strong> {{ $detail['ipk_kode'] ?? '-' }}<br>
                                                                                        <strong>Status:</strong> {{ $detail['ipk_status'] ?? '-' }}
                                                                                    </small>
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">
                                                                                @if($detail['has_okk'] ?? false)
                                                                                    <span class="badge bg-success">Ada</span>
                                                                                @else
                                                                                    <span class="badge bg-danger">Tidak</span>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($detail['has_okk'] ?? false)
                                                                                    <small>
                                                                                        <strong>Kode:</strong> {{ $detail['okk_kode'] ?? '-' }}<br>
                                                                                        <strong>Status:</strong> {{ $detail['okk_status'] ?? '-' }}
                                                                                    </small>
                                                                                @else
                                                                                    <span class="text-muted">-</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="6" class="text-center text-muted">Tidak ada data tanggal</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="mt-2">
                                                            <small class="text-muted">
                                                                <strong>Summary:</strong> 
                                                                IPK {{ $ipkCount }}/{{ $totalHari }} hari ({{ $totalHari > 0 ? round($ipkCount / $totalHari * 100) : 0 }}%) | 
                                                                OKK {{ $okkCount }}/{{ $totalHari }} hari ({{ $totalHari > 0 ? round($okkCount / $totalHari * 100) : 0 }}%)
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- JavaScript untuk Expand/Collapse --}}
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Toggle individual row
                                    document.querySelectorAll('.ikk-toggle-btn').forEach(function(btn) {
                                        btn.addEventListener('click', function() {
                                            var targetId = this.getAttribute('data-target');
                                            var targetRow = document.getElementById(targetId);
                                            var icon = this.querySelector('i');
                                            
                                            if (targetRow.classList.contains('d-none')) {
                                                targetRow.classList.remove('d-none');
                                                icon.textContent = 'remove';
                                                this.classList.remove('btn-outline-primary');
                                                this.classList.add('btn-primary');
                                            } else {
                                                targetRow.classList.add('d-none');
                                                icon.textContent = 'add';
                                                this.classList.remove('btn-primary');
                                                this.classList.add('btn-outline-primary');
                                            }
                                        });
                                    });
                                    
                                    // Expand All
                                    var btnExpandAll = document.getElementById('btnExpandAllIkk');
                                    if (btnExpandAll) {
                                        btnExpandAll.addEventListener('click', function() {
                                            document.querySelectorAll('.ikk-detail-row').forEach(function(row) {
                                                row.classList.remove('d-none');
                                            });
                                            document.querySelectorAll('.ikk-toggle-btn').forEach(function(btn) {
                                                btn.querySelector('i').textContent = 'remove';
                                                btn.classList.remove('btn-outline-primary');
                                                btn.classList.add('btn-primary');
                                            });
                                        });
                                    }
                                    
                                    // Collapse All
                                    var btnCollapseAll = document.getElementById('btnCollapseAllIkk');
                                    if (btnCollapseAll) {
                                        btnCollapseAll.addEventListener('click', function() {
                                            document.querySelectorAll('.ikk-detail-row').forEach(function(row) {
                                                row.classList.add('d-none');
                                            });
                                            document.querySelectorAll('.ikk-toggle-btn').forEach(function(btn) {
                                                btn.querySelector('i').textContent = 'add';
                                                btn.classList.remove('btn-primary');
                                                btn.classList.add('btn-outline-primary');
                                            });
                                        });
                                    }
                                });
                            </script>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="material-icons-outlined" style="font-size: 48px;">inbox</i>
                                <p class="mb-0 mt-2">Tidak ada data IKK (semua PIC APPROVED) untuk minggu ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Detail Work Permit IKK (full) --}}
        <div class="modal fade" id="modalIkkWorkPermitDetail" tabindex="-1" aria-labelledby="modalIkkWorkPermitDetailLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4 shadow-lg border">
                    <div class="modal-header bg-light rounded-top-4">
                        <h5 class="modal-title fw-bold text-dark" id="modalIkkWorkPermitDetailLabel">Detail Work Permit IKK</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-2 mb-2">Identitas</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width: 42%;">Kode IKK</td><td class="fw-semibold" id="wpModalCode">—</td></tr>
                                    <tr><td class="text-muted">Site</td><td id="wpModalSite">—</td></tr>
                                    <tr><td class="text-muted">Jenis Ijin Kerja Khusus</td><td id="wpModalJenis">—</td></tr>
                                    <tr><td class="text-muted">Nama Pekerjaan</td><td id="wpModalNamaPekerjaan">—</td></tr>
                                    <tr><td class="text-muted">Perusahaan</td><td id="wpModalPerusahaan">—</td></tr>
                                </table>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-2 mb-2">Periode & Status</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width: 42%;">Tanggal Mulai</td><td id="wpModalStartDate">—</td></tr>
                                    <tr><td class="text-muted">Tanggal Selesai</td><td id="wpModalEndDate">—</td></tr>
                                    <tr><td class="text-muted">Status WP</td><td><span class="badge bg-secondary" id="wpModalStatus">—</span></td></tr>
                                    <tr><td class="text-muted">Status Pekerjaan</td><td><span class="badge bg-info text-dark" id="wpModalStatusPekerjaan">—</span></td></tr>
                                    <tr><td class="text-muted">Status Matriks</td><td><span class="badge" id="wpModalStatusMatriks">—</span></td></tr>
                                </table>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-2 mb-2">Lokasi</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width: 42%;">Lokasi</td><td id="wpModalLocationName">—</td></tr>
                                    <tr><td class="text-muted">Detail Lokasi</td><td id="wpModalLocationDetail">—</td></tr>
                                </table>
                            </div>
                            <div class="col-12">
                                <h6 class="text-muted border-bottom pb-2 mb-2">Layer & PIC</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width: 42%;">PIC Approver</td><td class="fw-semibold text-primary" id="wpModalPicApprover">—</td></tr>
                                    <tr><td class="text-muted">Layer 1</td><td id="wpModalLayer1">—</td></tr>
                                    <tr><td class="text-muted">Layer 2</td><td id="wpModalLayer2">—</td></tr>
                                    <tr><td class="text-muted">Layer 3</td><td id="wpModalLayer3">—</td></tr>
                                    <tr><td class="text-muted">Layer 4</td><td id="wpModalLayer4">—</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
    
       
         
</div><!--end row-->
    </div><!--end collapse-->





</div>



    {{-- Modal Detail DOPM: Menampilkan IPK-IKK, OKK, OAK dalam satu tampilan tanpa tab --}}
    <div class="modal fade" id="detailDopmModal" tabindex="-1" aria-labelledby="detailDopmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-lg border border-light">
                <div class="modal-header rounded-top-4 py-3">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="material-icons-outlined me-2 fs-4 text-primary">assignment</span>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="detailDopmModalLabel">
                                <span id="detailDopmTitle">Detail DOPM</span>
                            </h5>
                            <small class="text-muted" id="detailDopmSubtitle">Kode IKK: —</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                {{-- Statistik IPK-IKK, OKK, OAK --}}
                <div class="modal-stat-cards px-4 py-3">
                    <p class="small fw-semibold text-muted mb-2">Statistik</p>
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="stat-card p-3 h-100">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons-outlined text-primary">checklist</span>
                                    <div>
                                        <span class="d-block fw-bold text-dark fs-5" id="statCountIpkIkk">0</span>
                                        <span class="small text-muted">IPK-IKK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card p-3 h-100">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons-outlined text-success">folder_open</span>
                                    <div>
                                        <span class="d-block fw-bold text-dark fs-5" id="statCountOkk">0</span>
                                        <span class="small text-muted">OKK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="stat-card p-3 h-100">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="material-icons-outlined text-warning">visibility</span>
                                    <div>
                                        <span class="d-block fw-bold text-dark fs-5" id="statCountOak">0</span>
                                        <span class="small text-muted">OAK</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="p-4 bg-white" id="detailDopmContent" style="max-height: 600px; overflow-y: auto;">
                        {{-- Data DOPM (dari database) --}}
                        <div class="card border mb-4 bg-light">
                            <div class="card-body py-3">
                                <h6 class="fw-bold mb-3 text-dark"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">description</i> Data DOPM</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-striped align-middle mb-0 small">
                                        <tbody>
                                            <tr><td class="text-muted fw-semibold" style="width: 140px;">ID DOP</td><td id="detailDopmId">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Kode IKK</td><td id="detailDopmKodeIkk">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Nama Pekerjaan</td><td id="detailDopmNamaPekerjaan" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Site</td><td id="detailDopmSite">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Perusahaan</td><td id="detailDopmPerusahaan" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Jenis IJK</td><td id="detailDopmJenisIjk" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Tanggal DOP</td><td id="detailDopmTanggal">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Status</td><td id="detailDopmStatus">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Lokasi</td><td id="detailDopmLocation" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Detail Lokasi</td><td id="detailDopmLocationDetail" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 1</td><td id="detailDopmLayer1" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 2</td><td id="detailDopmLayer2" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 3</td><td id="detailDopmLayer3" class="text-break">—</td></tr>
                                            <tr><td class="text-muted fw-semibold">Layer 4</td><td id="detailDopmLayer4" class="text-break">—</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-warning btn-sm btn-intervensi-from-detail" id="btnIntervensiFromDetailTop" title="Intervensi (IPK-IKK, OKK, OAK)">
                                        <i class="material-icons-outlined align-middle me-1" style="font-size: 16px;">campaign</i> Intervensi
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- Section IPK-IKK --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="material-icons-outlined text-primary me-2" style="font-size: 20px;">checklist</i>
                                <h6 class="mb-0 fw-bold">IPK-IKK <span class="badge bg-primary ms-2" id="badgeIpkIkk">0</span><span class="small text-muted ms-1" id="ipkIkkSourceLabel"></span></h6>
                            </div>
                            <div id="ipkIkkLoading" class="text-center py-4 d-none bg-white">
                                <div class="spinner-border text-primary mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data IPK-IKK...</p>
                            </div>
                            <div id="ipkIkkEmpty" class="text-center py-4 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data IPK-IKK untuk kode IKK ini.</p>
                            </div>
                            <div id="ipkIkkTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2">Tabel di bawah menampilkan seluruh data IPK-IKK dengan kode IKK ini.</p>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableIpkIkk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Durasi</th><th>CCTV</th><th>Kategori IJK</th><th>Status</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Section OKK --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="material-icons-outlined text-success me-2" style="font-size: 20px;">folder_open</i>
                                <h6 class="mb-0 fw-bold">OKK <span class="badge bg-success ms-2" id="badgeOkk">0</span><span class="small text-muted ms-1" id="okkSourceLabel"></span></h6>
                            </div>
                            <div id="okkLoading" class="text-center py-4 d-none bg-white">
                                <div class="spinner-border text-success mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data OKK...</p>
                            </div>
                            <div id="okkEmpty" class="text-center py-4 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data OKK untuk kode IKK ini.</p>
                            </div>
                            <div id="okkTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2">Tabel di bawah menampilkan seluruh data OKK dengan kode IKK ini.</p>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableOkk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Jenis IJK</th><th>Layer</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Section OAK --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="material-icons-outlined text-warning me-2" style="font-size: 20px;">visibility</i>
                                <h6 class="mb-0 fw-bold">OAK <span class="badge bg-warning text-dark ms-2" id="badgeOak">0</span></h6>
                            </div>
                            <div id="oakContext" class="card border mb-3 d-none bg-white">
                                <div class="card-body py-2 px-3">
                                    <small class="text-muted fw-semibold">Layer 2 / 3 / 4:</small>
                                    <span id="oakLayerNames" class="ms-1">—</span>
                                </div>
                            </div>
                            <div id="oakLoading" class="text-center py-4 d-none bg-white">
                                <div class="spinner-border text-warning mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data OAK...</p>
                            </div>
                            <div id="oakEmpty" class="text-center py-4 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data OAK untuk kriteria ini.</p>
                            </div>
                            <div id="oakTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2">Tabel di bawah menampilkan data Observasi Area Kerja sesuai activity dan SID.</p>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableOak">
                                        <thead class="table-light"><tr><th>Activity</th><th>Sub Activity</th><th>Submit Date</th><th>Submit By</th><th>SID Pelapor</th><th>Lokasi</th><th>Detail Lokasi</th><th>Conclusion</th><th>Site</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger btn-intervensi-from-detail" id="btnIntervensiFromDetail" title="Intervensi (IPK-IKK, OKK, OAK)">
                        <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">campaign</i> Intervensi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Intervensi DOPM: satu modal, 3 section (IPK-IKK, OKK, OAK) + Layer masing-masing, tanpa tab --}}
    <div class="modal fade" id="intervensiDopmModal" tabindex="-1" aria-labelledby="intervensiDopmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg border border-light">
                <div class="modal-header rounded-top-4 py-3 bg-warning bg-opacity-10">
                    <div class="d-flex align-items-center flex-grow-1">
                        <span class="material-icons-outlined me-2 fs-4 text-warning">campaign</span>
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="intervensiDopmModalLabel">
                                <span id="intervensiDopmTitle">Intervensi DOPM</span>
                            </h5>
                            <small class="text-muted" id="intervensiDopmSubtitle">Kode IKK: —</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    {{-- Section 1: IPK-IKK + Layer 1 --}}
                    <div class="intervensi-section mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">checklist</i> IPK-IKK <span class="badge bg-primary ms-1" id="intervensiBadgeIpk">0</span></h6>
                        <div id="intervensiLayer1Wrap" class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-warning">notifications_active</span>
                                <strong>Layer 1 — Pengingat Isi IPK (INSPEKSI PRA KERJA)</strong>
                            </div>
                            <div class="card-body py-3">
                                <p class="small mb-2"><strong>Nama Layer:</strong> <span id="intervensiLayer1NameDisplay" class="text-dark">—</span></p>
                                <p class="small text-muted mb-2">Pilih PIC Layer 1 yang akan diintervensi, lalu klik tombol Intervensi.</p>
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-md-6">
                                        <select id="intervensiLayer1Select" class="form-select form-select-sm d-none">
                                            <option value="">Pilih PIC Layer 1...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" id="intervensiLayer1Btn" class="btn btn-sm btn-success w-100 d-none">
                                            <i class="material-icons-outlined me-1" style="font-size:16px;">send</i> Intervensi by WA (IPK)
                                        </button>
                                    </div>
                                </div>
                                <div id="intervensiLayer1Users" class="d-flex flex-wrap gap-2 d-none"></div>
                                <div id="intervensiLayer1Empty" class="text-muted small d-none">Tidak ada user terdaftar untuk Layer 1 ini.</div>
                                <div id="intervensiLayer1NoName" class="text-muted small d-none">Kolom <strong>SID Layer 1</strong> atau <strong>Nama Layer 1</strong> untuk DOPM ini belum diisi. Silakan edit data DOPM untuk menampilkan daftar PIC dan tombol Intervensi by WA.</div>
                                <div id="intervensiLayer1Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Memuat daftar PIC Layer 1...</div>
                            </div>
                        </div>
                        <div id="intervensiIpkLoading" class="text-center py-3 d-none"><div class="spinner-border text-primary spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data IPK-IKK...</p></div>
                        <div id="intervensiIpkEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data IPK-IKK.</p></div>
                        <div id="intervensiIpkTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableIpk">
                                    <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Durasi</th><th>CCTV</th><th>Kategori IJK</th><th>Status</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: OKK + Layer 1 --}}
                    <div class="intervensi-section mb-4">
                        <h6 class="text-success border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">folder_open</i> OKK <span class="badge bg-success ms-1" id="intervensiBadgeOkk">0</span></h6>
                        <div id="intervensiOkkLayer1Wrap" class="card border-success mb-3">
                            <div class="card-header bg-success bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-success">notifications_active</span>
                                <strong>Layer 1 — Intervensi OKK (OBSERVASI KEGIATAN KERJA)</strong>
                            </div>
                            <div class="card-body py-3">
                                <p class="small mb-2"><strong>Nama Layer:</strong> <span id="intervensiOkkLayer1NameDisplay" class="text-dark">—</span></p>
                                <p class="small text-muted mb-2">Pilih PIC Layer 1 yang akan diintervensi untuk OKK, lalu klik tombol Intervensi.</p>
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col-md-6">
                                        <select id="intervensiOkkLayer1Select" class="form-select form-select-sm d-none">
                                            <option value="">Pilih PIC Layer 1...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" id="intervensiOkkLayer1Btn" class="btn btn-sm btn-success w-100 d-none">
                                            <i class="material-icons-outlined me-1" style="font-size:16px;">send</i> Intervensi by WA (OKK)
                                        </button>
                                    </div>
                                </div>
                                <div id="intervensiOkkLayer1Users" class="d-flex flex-wrap gap-2 d-none"></div>
                                <div id="intervensiOkkLayer1Empty" class="text-muted small d-none">Tidak ada user terdaftar untuk Layer 1 ini.</div>
                                <div id="intervensiOkkLayer1NoName" class="text-muted small d-none">Kolom <strong>SID Layer 1</strong> atau <strong>Nama Layer 1</strong> untuk DOPM ini belum diisi.</div>
                                <div id="intervensiOkkLayer1Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Memuat daftar PIC Layer 1...</div>
                            </div>
                        </div>
                        <div id="intervensiOkkLoading" class="text-center py-3 d-none"><div class="spinner-border text-success spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data OKK...</p></div>
                        <div id="intervensiOkkEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data OKK.</p></div>
                        <div id="intervensiOkkTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOkk">
                                    <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Jenis IJK</th><th>Layer</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: OAK + Intervensi Layer 2, 3, 4 --}}
                    <div class="intervensi-section">
                        <h6 class="text-warning text-dark border-bottom pb-2 mb-3"><i class="material-icons-outlined align-middle me-1" style="font-size:20px;">visibility</i> OAK <span class="badge bg-warning text-dark ms-1" id="intervensiBadgeOak">0</span></h6>
                        {{-- Intervensi OAK: Layer 2, Layer 3, Layer 4 — 3 tombol intervensi by WA --}}
                        <div id="intervensiOakLayersWrap" class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-10 py-2">
                                <span class="material-icons-outlined align-middle me-1 text-warning">notifications_active</span>
                                <strong>Intervensi OAK — Layer 2, 3, 4</strong>
                            </div>
                            <div class="card-body py-3">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 2</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer2Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer2Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer2Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer2Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 3</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer3Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer3Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer3Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer3Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-2 bg-light">
                                            <p class="small mb-1 fw-semibold">Layer 4</p>
                                            <p class="small mb-1 text-muted"><strong>Nama:</strong> <span id="intervensiOakLayer4Name" class="text-dark">—</span></p>
                                            <div id="intervensiOakLayer4Users" class="d-flex flex-wrap gap-1"></div>
                                            <div id="intervensiOakLayer4Empty" class="text-muted small d-none">Tidak ada user.</div>
                                            <div id="intervensiOakLayer4Loading" class="text-muted small d-none"><span class="spinner-border spinner-border-sm me-1"></span>Memuat...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="intervensiOakLoading" class="text-center py-3 d-none"><div class="spinner-border text-warning spinner-border-sm" role="status"></div><p class="text-muted mb-0 mt-2 small">Memuat data OAK...</p></div>
                        <div id="intervensiOakEmpty" class="text-center py-3 d-none"><span class="material-icons-outlined text-muted" style="font-size: 32px;">inbox</span><p class="text-muted mt-2 mb-0 small">Tidak ada data OAK.</p></div>
                        <div id="intervensiOakTableWrap" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOak">
                                    <thead class="table-light"><tr><th>Activity</th><th>Sub Activity</th><th>Submit Date</th><th>Submit By</th><th>SID Pelapor</th><th>Lokasi</th><th>Detail Lokasi</th><th>Conclusion</th><th>Site</th></tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>







@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ol@8.2.0/dist/ol.js"></script>
<script src="https://cdn.jsdelivr.net/npm/proj4@2.9.0/dist/proj4.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.7/dist/hls.min.js"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('build/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<!-- Load BMO2 PAMA GeoJSON data -->


<script src="{{ URL::asset('build/plugins/apexchart/apexcharts.min.js') }}"></script>
<script>window.skipChart4 = true; window.skipChart1 = true;</script>
<script src="{{ URL::asset('build/js/index.js') }}"></script>
<script src="{{ URL::asset('build/plugins/peity/jquery.peity.min.js') }}"></script>
<script>
(function() {
  // Bootstrap Week Picker
  var selectedWeekValue = @json($filterWeek ?? '');
  var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
  var monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
  var dayNamesShort = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
  
  var currentDate = new Date();
  var displayMonth = currentDate.getMonth();
  var displayYear = currentDate.getFullYear();
  var isOpen = false;
  
  var inputEl = document.getElementById('weekPickerInput');
  var containerEl = document.getElementById('weekPickerContainer');
  var hiddenInput = document.getElementById('filterWeek');
  
  if (selectedWeekValue) {
    var match = selectedWeekValue.match(/^(\d{4})-W(\d{2})$/);
    if (match) {
      var y = parseInt(match[1], 10);
      var w = parseInt(match[2], 10);
      var firstDayOfWeek = getDateOfISOWeek(w, y);
      displayMonth = firstDayOfWeek.getMonth();
      displayYear = firstDayOfWeek.getFullYear();
    }
  }
  
  function getDateOfISOWeek(week, year) {
    var simple = new Date(year, 0, 1 + (week - 1) * 7);
    var dow = simple.getDay();
    var ISOweekStart = simple;
    if (dow <= 4) {
      ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
    } else {
      ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
    }
    return ISOweekStart;
  }
  
  function getISOWeek(date) {
    var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    var dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
    return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
  }
  
  function getISOWeekYear(date) {
    var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    var dayNum = d.getUTCDay() || 7;
    d.setUTCDate(d.getUTCDate() + 4 - dayNum);
    return d.getUTCFullYear();
  }
  
  function formatWeekValue(year, week) {
    return year + '-W' + String(week).padStart(2, '0');
  }
  
  function formatDate(date) {
    return date.getDate() + ' ' + monthNamesShort[date.getMonth()];
  }
  
  function formatFullDate(date) {
    return date.getDate() + ' ' + monthNamesShort[date.getMonth()] + ' ' + date.getFullYear();
  }
  
  function isToday(date) {
    var today = new Date();
    return date.getDate() === today.getDate() && 
           date.getMonth() === today.getMonth() && 
           date.getFullYear() === today.getFullYear();
  }
  
  function renderWeekPicker() {
    if (!containerEl) return;
    
    var firstDay = new Date(displayYear, displayMonth, 1);
    var lastDay = new Date(displayYear, displayMonth + 1, 0);
    
    var startDate = new Date(firstDay);
    var dayOfWeek = startDate.getDay() || 7;
    startDate.setDate(startDate.getDate() - dayOfWeek + 1);
    
    var endDate = new Date(lastDay);
    var endDayOfWeek = endDate.getDay() || 7;
    endDate.setDate(endDate.getDate() + (7 - endDayOfWeek));
    
    var html = '<div class="week-picker-header">';
    html += '<button type="button" class="btn-nav" id="wpPrev"><i class="material-icons-outlined" style="font-size:18px;">chevron_left</i></button>';
    html += '<span class="month-year">' + monthNames[displayMonth] + ' ' + displayYear + '</span>';
    html += '<button type="button" class="btn-nav" id="wpNext"><i class="material-icons-outlined" style="font-size:18px;">chevron_right</i></button>';
    html += '</div>';
    
    html += '<table class="week-picker-table">';
    html += '<thead><tr><th>Wk</th>';
    for (var i = 0; i < 7; i++) {
      html += '<th>' + dayNamesShort[i] + '</th>';
    }
    html += '</tr></thead><tbody>';
    
    var currentDateIter = new Date(startDate);
    
    while (currentDateIter <= endDate) {
      var weekNum = getISOWeek(currentDateIter);
      var weekYear = getISOWeekYear(currentDateIter);
      var weekValue = formatWeekValue(weekYear, weekNum);
      var isSelected = weekValue === selectedWeekValue;
      
      var weekStart = new Date(currentDateIter);
      var weekEnd = new Date(currentDateIter);
      weekEnd.setDate(weekEnd.getDate() + 6);
      
      html += '<tr class="week-row' + (isSelected ? ' selected' : '') + '" data-week="' + weekValue + '" data-start="' + formatDate(weekStart) + '" data-end="' + formatFullDate(weekEnd) + '">';
      html += '<td class="week-num">' + weekNum + '</td>';
      
      for (var d = 0; d < 7; d++) {
        var dayDate = new Date(currentDateIter);
        dayDate.setDate(dayDate.getDate() + d);
        
        var classes = [];
        if (dayDate.getMonth() !== displayMonth) classes.push('other-month');
        if (isToday(dayDate)) classes.push('today');
        
        html += '<td class="' + classes.join(' ') + '">' + dayDate.getDate() + '</td>';
      }
      
      html += '</tr>';
      currentDateIter.setDate(currentDateIter.getDate() + 7);
    }
    
    html += '</tbody></table>';
    
    containerEl.querySelector('.week-picker-calendar').innerHTML = html;
    
    // Add event listeners
    containerEl.querySelector('#wpPrev')?.addEventListener('click', function(e) {
      e.stopPropagation();
      displayMonth--;
      if (displayMonth < 0) { displayMonth = 11; displayYear--; }
      renderWeekPicker();
    });
    
    containerEl.querySelector('#wpNext')?.addEventListener('click', function(e) {
      e.stopPropagation();
      displayMonth++;
      if (displayMonth > 11) { displayMonth = 0; displayYear++; }
      renderWeekPicker();
    });
    
    containerEl.querySelectorAll('.week-row').forEach(function(row) {
      row.addEventListener('click', function() {
        var weekVal = this.getAttribute('data-week');
        var start = this.getAttribute('data-start');
        var end = this.getAttribute('data-end');
        selectWeek(weekVal, start, end);
      });
    });
  }
  
  function selectWeek(weekValue, startDisplay, endDisplay) {
    selectedWeekValue = weekValue;
    
    if (hiddenInput) hiddenInput.value = weekValue;
    
    var match = weekValue.match(/^(\d{4})-W(\d{2})$/);
    if (match && inputEl) {
      var w = parseInt(match[2], 10);
      inputEl.value = 'Week ' + w + ': ' + startDisplay + ' - ' + endDisplay;
    }
    
    closeDropdown();
  }
  
  function openDropdown() {
    if (!containerEl || !inputEl) return;
    isOpen = true;
    containerEl.classList.remove('d-none');
    
    // Position dropdown below input
    var rect = inputEl.getBoundingClientRect();
    containerEl.style.top = (inputEl.offsetTop + inputEl.offsetHeight + 4) + 'px';
    containerEl.style.left = inputEl.offsetLeft + 'px';
    
    renderWeekPicker();
  }
  
  function closeDropdown() {
    if (!containerEl) return;
    isOpen = false;
    containerEl.classList.add('d-none');
  }
  
  function toggleDropdown() {
    if (isOpen) {
      closeDropdown();
    } else {
      openDropdown();
    }
  }
  
  // Event listeners
  if (inputEl) {
    inputEl.addEventListener('click', function(e) {
      e.stopPropagation();
      toggleDropdown();
    });
  }
  
  // Close on click outside
  document.addEventListener('click', function(e) {
    if (isOpen && containerEl && !containerEl.contains(e.target) && e.target !== inputEl) {
      closeDropdown();
    }
  });
  
  // Close on escape
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && isOpen) {
      closeDropdown();
    }
  });
})();
</script>

<script>
(function() {
  // Chart1: Total IKK Week ini (ClickHouse Approved + Expired) per hari
  @php
    $chart1WeekData = $chartIkkClickhousePerHariMinggu ?? array_fill(0, 7, 0);
  @endphp
  var chart1Data = @json($chart1WeekData);
  setTimeout(function renderChart1() {
    var el = document.querySelector('#chart1');
    if (!el || typeof ApexCharts === 'undefined') return;
    // Pastikan chart tidak dirender dua kali
    try { ApexCharts.exec('chart1', 'destroy'); } catch (e) {}
    el.innerHTML = '';
    new ApexCharts(el, {
      chart: { id: 'chart1', height: 105, type: 'area', sparkline: { enabled: true }, zoom: { enabled: false }, fontFamily: 'inherit' },
      series: [{ name: 'IKK', data: chart1Data }],
      dataLabels: { enabled: false },
      stroke: { width: 1.7, curve: 'smooth' },
      fill: { type: 'gradient', gradient: { shade: 'dark', gradientToColors: ['#02c27a'], shadeIntensity: 1, type: 'vertical', opacityFrom: 0.5, opacityTo: 0 } },
      colors: ['#02c27a'],
      xaxis: { labels: { show: false } },
      yaxis: { labels: { show: false } },
      grid: { borderColor: 'rgba(0,0,0,0.05)', strokeDashArray: 4 },
      tooltip: { y: { title: { formatter: function() { return 'IKK'; } } } }
    }).render();
  }, 300);

  // Chart jumlah izin kerja per jenis (chart4 dihapus — diganti Kalender Compliance)
})();
</script>

<script>
(function() {
  var filterDateStr = @json($filterDate ?? now()->toDateString());
  var complianceByDay = @json($complianceByDay ?? []);
  var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

  function parseFilterDate() {
    var m = filterDateStr.match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (m) return { year: parseInt(m[1], 10), month: parseInt(m[2], 10) - 1, day: parseInt(m[3], 10) };
    var d = new Date();
    return { year: d.getFullYear(), month: d.getMonth(), day: d.getDate() };
  }
  var filterDateParsed = parseFilterDate();
  var displayMonth = filterDateParsed.month;
  var displayYear = filterDateParsed.year;

  function getStatusClass(pct) {
    if (pct <= 50) return 'negative';
    if (pct <= 80) return 'neutral';
    return 'positive';
  }

  function renderComplianceCalendar(month, year) {
    var container = document.getElementById('complianceCalendarDays');
    if (!container) return;
    container.innerHTML = '';
    document.getElementById('complianceCurrentMonth').textContent = monthNames[month] + ' ' + year;

    var firstDay = new Date(year, month, 1).getDay();
    var daysInMonth = new Date(year, month + 1, 0).getDate();
    var filterForm = document.getElementById('dashboardFilterForm');
    var filterInput = document.getElementById('filterDate');
    var isFilterDayInThisMonth = (year === filterDateParsed.year && month === filterDateParsed.month);

    for (var i = 0; i < firstDay; i++) {
      var empty = document.createElement('div');
      empty.className = 'compliance-day-cell empty';
      empty.style.cursor = 'default';
      container.appendChild(empty);
    }

    for (var day = 1; day <= daysInMonth; day++) {
      var cell = document.createElement('div');
      var dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
      var pct = complianceByDay[dateStr] != null ? Number(complianceByDay[dateStr]) : null;
      var isFilterDay = isFilterDayInThisMonth && (day === filterDateParsed.day);

      if (pct != null && !isNaN(pct)) {
        var statusClass = getStatusClass(pct);
        cell.className = 'compliance-day-cell ' + statusClass;
        cell.innerHTML = '<div class="compliance-day-number">' + day + '</div>' +
          '<div class="compliance-day-value">' + pct + '%</div>' +
          '<div class="compliance-day-label">Compliance IKK</div>';
        cell.style.cursor = 'pointer';
        cell.addEventListener('click', function(selectedDate) {
          return function() {
            if (filterInput) filterInput.value = selectedDate;
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                title: 'Memuat data...',
                html: 'Menampilkan data untuk tanggal <strong>' + selectedDate + '</strong>. Mohon tunggu.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                  Swal.showLoading();
                  if (filterForm) filterForm.submit();
                }
              });
            } else if (filterForm) {
              filterForm.submit();
            }
          };
        }(dateStr));
      } else {
        cell.className = 'compliance-day-cell empty';
        cell.innerHTML = '<div class="compliance-day-number">' + day + '</div>' +
          '<div class="compliance-day-value">—</div>' +
          '<div class="compliance-day-label">' + (isFilterDay ? 'Tanggal dipilih' : 'Klik untuk pilih') + '</div>';
        cell.style.cursor = 'pointer';
        cell.addEventListener('click', function(selectedDate) {
          return function() {
            if (filterInput) filterInput.value = selectedDate;
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                title: 'Memuat data...',
                html: 'Menampilkan data untuk tanggal <strong>' + selectedDate + '</strong>. Mohon tunggu.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                  Swal.showLoading();
                  if (filterForm) filterForm.submit();
                }
              });
            } else if (filterForm) {
              filterForm.submit();
            }
          };
        }(dateStr));
      }
      container.appendChild(cell);
    }
  }

  var prevBtn = document.getElementById('compliancePrevMonth');
  var nextBtn = document.getElementById('complianceNextMonth');
  if (prevBtn) {
    prevBtn.addEventListener('click', function() {
      displayMonth--;
      if (displayMonth < 0) { displayMonth = 11; displayYear--; }
      renderComplianceCalendar(displayMonth, displayYear);
    });
  }
  if (nextBtn) {
    nextBtn.addEventListener('click', function() {
      displayMonth++;
      if (displayMonth > 11) { displayMonth = 0; displayYear++; }
      renderComplianceCalendar(displayMonth, displayYear);
    });
  }

  function initComplianceCalendar() {
    var container = document.getElementById('complianceCalendarDays');
    if (container) {
      renderComplianceCalendar(displayMonth, displayYear);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initComplianceCalendar);
  } else {
    initComplianceCalendar();
  }
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    // Loading SweetAlert saat submit filter tanggal
    var filterForm = document.getElementById('dashboardFilterForm');
    if (filterForm && typeof Swal !== 'undefined') {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Memuat data...',
                html: 'Menampilkan data untuk tanggal yang dipilih.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });
            filterForm.submit();
        });
    }

    var modalApiUrl = @json(route('dopmikk.api.ikk-modal-data'));
    var layer1UsersApiUrl = @json(route('dopmikk.api.layer1-users'));
    var layers234UsersApiUrl = @json(route('dopmikk.api.layers234-users'));
    var ipkFormLink = 'https://docs.google.com/forms/d/e/1FAIpQLSddTpsj6qbXN3pSpHvhZvPJdM4CU10H3oY9k3MEg6NTzRublA/viewform';
    var modalEl = document.getElementById('detailDopmModal');
    var intervensiModalEl = document.getElementById('intervensiDopmModal');
    var intervensiModal = intervensiModalEl ? new bootstrap.Modal(intervensiModalEl) : null;
    var modal = modalEl ? new bootstrap.Modal(modalEl) : null;

    // DataTables untuk tabel DOPM harian
    if ($.fn.DataTable && document.getElementById('tableDopmHarian')) {
        $('#tableDopmHarian').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ data)',
                paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            },
            columnDefs: [{ targets: [9, 10], orderable: false }],
            dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }

    // DataTables untuk tabel IKK ClickHouse harian
    if ($.fn.DataTable && document.getElementById('tableIkkClickhouseHarian')) {
        $('#tableIkkClickhouseHarian').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ baris',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ data)',
                paginate: { first: 'Awal', last: 'Akhir', next: 'Selanjutnya', previous: 'Sebelumnya' }
            },
            dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }

    // Modal detail Work Permit IKK: klik baris tabel → tampilkan data lengkap
    (function() {
        var modalEl = document.getElementById('modalIkkWorkPermitDetail');
        if (!modalEl) return;
        function setEl(id, text) {
            var el = document.getElementById(id);
            if (el) el.textContent = text != null && text !== '' ? String(text) : '—';
        }
        function openIkkDetailModal(rowEl) {
            var raw = rowEl.getAttribute ? rowEl.getAttribute('data-ikk') : $(rowEl).attr('data-ikk');
            if (!raw) return;
            var data;
            try { data = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch (e) { return; }
            setEl('wpModalCode', data.code);
            setEl('wpModalSite', data.site);
            setEl('wpModalJenis', data.jenis_ijin_kerja_khusus);
            setEl('wpModalNamaPekerjaan', data.nama_pekerjaan);
            setEl('wpModalPerusahaan', data.perusahaan);
            setEl('wpModalStartDate', data.start_date);
            setEl('wpModalEndDate', data.end_date);
            var statusEl = document.getElementById('wpModalStatus');
            if (statusEl) statusEl.textContent = data.status || '—';
            var statusPekerjaanEl = document.getElementById('wpModalStatusPekerjaan');
            if (statusPekerjaanEl) statusPekerjaanEl.textContent = data.status_pekerjaan || 'Tidak ada IPK';
            var matriksEl = document.getElementById('wpModalStatusMatriks');
            if (matriksEl) {
                matriksEl.textContent = data.status_matriks || '—';
                matriksEl.className = 'badge ' + (data.status_matriks === 'Hijau' ? 'bg-success' : (data.status_matriks === 'Kuning' ? 'bg-warning text-dark' : 'bg-danger'));
            }
            setEl('wpModalLocationName', data.location_name);
            setEl('wpModalLocationDetail', data.location_detail_name);
            setEl('wpModalPicApprover', data.pic_approver_name);
            setEl('wpModalLayer1', data.nama_layer_1);
            setEl('wpModalLayer2', data.nama_layer_2);
            setEl('wpModalLayer3', data.nama_layer_3);
            setEl('wpModalLayer4', data.nama_layer_4);
            var modalInstance = typeof bootstrap !== 'undefined' && bootstrap.Modal ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;
            if (modalInstance) modalInstance.show();
        }
        // Klik salah satu kolom (seluruh baris) → buka modal detail (delegate: tr dengan data-ikk)
        $(document).on('click', '#tableIkkClickhouseHarian tbody tr[data-ikk]', function(e) {
            e.preventDefault();
            openIkkDetailModal(this);
        });
        $(document).on('keydown', '#tableIkkClickhouseHarian tbody tr[data-ikk]', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openIkkDetailModal(this);
            }
        });
    })();

    function showLoading(panel) {
        var loadingEl = document.getElementById(panel + 'Loading');
        var emptyEl = document.getElementById(panel + 'Empty');
        var wrapEl = document.getElementById(panel + 'TableWrap');
        if (loadingEl) { loadingEl.classList.remove('d-none'); loadingEl.style.display = 'block'; }
        if (emptyEl) { emptyEl.classList.add('d-none'); emptyEl.style.display = 'none'; }
        if (wrapEl) { wrapEl.classList.add('d-none'); wrapEl.style.display = 'none'; }
    }
    function showEmpty(panel) {
        var loadingEl = document.getElementById(panel + 'Loading');
        var emptyEl = document.getElementById(panel + 'Empty');
        var wrapEl = document.getElementById(panel + 'TableWrap');
        if (loadingEl) { loadingEl.classList.add('d-none'); loadingEl.style.display = 'none'; }
        if (emptyEl) { emptyEl.classList.remove('d-none'); emptyEl.style.display = 'block'; }
        if (wrapEl) { wrapEl.classList.add('d-none'); wrapEl.style.display = 'none'; }
    }
    function showTable(panel) {
        var loadingEl = document.getElementById(panel + 'Loading');
        var emptyEl = document.getElementById(panel + 'Empty');
        var wrapEl = document.getElementById(panel + 'TableWrap');
        if (loadingEl) { loadingEl.classList.add('d-none'); loadingEl.style.display = 'none'; }
        if (emptyEl) { emptyEl.classList.add('d-none'); emptyEl.style.display = 'none'; }
        if (wrapEl) { wrapEl.classList.remove('d-none'); wrapEl.style.display = 'block'; }
    }

    function tr(cells) {
        var row = document.createElement('tr');
        cells.forEach(function(c) {
            var td = document.createElement('td');
            td.textContent = c == null || c === undefined ? '—' : String(c);
            row.appendChild(td);
        });
        return row;
    }
    function safeStr(val, maxLen) {
        if (val == null || val === undefined) return '—';
        var s = String(val).trim();
        if (!s) return '—';
        if (maxLen && s.length > maxLen) s = s.substring(0, maxLen);
        return s;
    }
    function formatTs(ts) {
        if (!ts) return '—';
        var s = String(ts).trim();
        if (!s) return '—';
        var m = s.match(/^(\d{4})-(\d{2})-(\d{2})[T\s](\d{2}):(\d{2})/);
        if (m) return m[3] + '/' + m[2] + '/' + m[1] + ' ' + m[4] + ':' + m[5];
        return s;
    }


    // Normalisasi nomor untuk wa.me: 08xxx -> 62xxx
    function normalizeWaNumber(selular) {
        if (!selular || typeof selular !== 'string') return '';
        var s = selular.replace(/\s+/g, '').replace(/-/g, '');
        if (/^0\d+/.test(s)) return '62' + s.substring(1);
        if (!/^62/.test(s) && /^\d+/.test(s)) return '62' + s;
        return s;
    }

    // Event delegation: tombol Intervensi
    document.addEventListener('click', function(e) {
        var btnIntervensi = e.target.closest('.btn-intervensi-dopm');
        if (btnIntervensi && intervensiModal) {
            var data = JSON.parse(btnIntervensi.getAttribute('data-dopm') || '{}');
            var namaLayer1 = (data.nama_layer_1 || '').trim();
            var sidLayer1 = (data.sid_layer_1 || '').trim();
            var hasLayer1 = sidLayer1 !== '' || namaLayer1 !== '';
            document.getElementById('intervensiDopmTitle').textContent = (data.id_dop || 'Intervensi') + ' — ' + (data.nama_pekerjaan || 'DOPM').substring(0, 50);
            document.getElementById('intervensiDopmSubtitle').textContent = 'Kode IKK: ' + (data.kode_ikk || '—');
            document.getElementById('intervensiBadgeIpk').textContent = '0';
            document.getElementById('intervensiBadgeOkk').textContent = '0';
            document.getElementById('intervensiBadgeOak').textContent = '0';
            document.getElementById('intervensiIpkLoading').classList.remove('d-none');
            document.getElementById('intervensiIpkEmpty').classList.add('d-none');
            document.getElementById('intervensiIpkTableWrap').classList.add('d-none');
            document.getElementById('intervensiOkkLoading').classList.remove('d-none');
            document.getElementById('intervensiOkkEmpty').classList.add('d-none');
            document.getElementById('intervensiOkkTableWrap').classList.add('d-none');
            document.getElementById('intervensiOakLoading').classList.remove('d-none');
            document.getElementById('intervensiOakEmpty').classList.add('d-none');
            document.getElementById('intervensiOakTableWrap').classList.add('d-none');
            [2, 3, 4].forEach(function(n) {
                var usersEl = document.getElementById('intervensiOakLayer' + n + 'Users');
                var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                var nameEl = document.getElementById('intervensiOakLayer' + n + 'Name');
                if (usersEl) usersEl.innerHTML = '';
                if (emptyEl) { emptyEl.classList.add('d-none'); }
                if (loadingEl) { loadingEl.classList.remove('d-none'); }
                if (nameEl) nameEl.textContent = '—';
            });
            var layer1Wrap = document.getElementById('intervensiLayer1Wrap');
            var layer1UsersEl = document.getElementById('intervensiLayer1Users');
            var layer1EmptyEl = document.getElementById('intervensiLayer1Empty');
            var layer1NoNameEl = document.getElementById('intervensiLayer1NoName');
            var layer1LoadingEl = document.getElementById('intervensiLayer1Loading');
            var layer1SelectEl = document.getElementById('intervensiLayer1Select');
            var layer1BtnEl = document.getElementById('intervensiLayer1Btn');
            var okkLayer1Wrap = document.getElementById('intervensiOkkLayer1Wrap');
            var okkLayer1NameDisplay = document.getElementById('intervensiOkkLayer1NameDisplay');
            var okkLayer1UsersEl = document.getElementById('intervensiOkkLayer1Users');
            var okkLayer1EmptyEl = document.getElementById('intervensiOkkLayer1Empty');
            var okkLayer1NoNameEl = document.getElementById('intervensiOkkLayer1NoName');
            var okkLayer1LoadingEl = document.getElementById('intervensiOkkLayer1Loading');
            var okkLayer1SelectEl = document.getElementById('intervensiOkkLayer1Select');
            var okkLayer1BtnEl = document.getElementById('intervensiOkkLayer1Btn');
            layer1Wrap.classList.remove('d-none');
            layer1UsersEl.innerHTML = '';
            document.getElementById('intervensiLayer1NameDisplay').textContent = namaLayer1 || '—';
            layer1EmptyEl.classList.add('d-none');
            layer1NoNameEl.classList.add('d-none');
            layer1LoadingEl.classList.add('d-none');
            if (layer1SelectEl) { layer1SelectEl.classList.add('d-none'); layer1SelectEl.innerHTML = '<option value=\"\">Pilih PIC Layer 1...</option>'; }
            if (layer1BtnEl) { layer1BtnEl.classList.add('d-none'); layer1BtnEl.removeAttribute('data-message'); }
            if (okkLayer1Wrap) {
                okkLayer1Wrap.classList.remove('d-none');
                okkLayer1UsersEl.innerHTML = '';
                okkLayer1NameDisplay.textContent = namaLayer1 || '—';
                okkLayer1EmptyEl.classList.add('d-none');
                okkLayer1NoNameEl.classList.add('d-none');
                okkLayer1LoadingEl.classList.add('d-none');
                if (okkLayer1SelectEl) { okkLayer1SelectEl.classList.add('d-none'); okkLayer1SelectEl.innerHTML = '<option value=\"\">Pilih PIC Layer 1...</option>'; }
                if (okkLayer1BtnEl) { okkLayer1BtnEl.classList.add('d-none'); okkLayer1BtnEl.removeAttribute('data-message'); }
            }
            if (!hasLayer1) {
                layer1NoNameEl.classList.remove('d-none');
                if (okkLayer1NoNameEl) okkLayer1NoNameEl.classList.remove('d-none');
            } else {
                layer1LoadingEl.classList.remove('d-none');
                if (okkLayer1LoadingEl) okkLayer1LoadingEl.classList.remove('d-none');
            }
            intervensiModal.show();

            var params = new URLSearchParams({
                kode_ikk: data.kode_ikk || '',
                work_permit_id: data.work_permit_id || '',
                tanggal_dop: data.tanggal_dop || '',
                jenis_ijin_kerja_khusus: data.jenis_ijin_kerja_khusus || '',
                sid_layer_2: data.sid_layer_2 || '',
                sid_layer_3: data.sid_layer_3 || '',
                sid_layer_4: data.sid_layer_4 || '',
                nama_layer_2: data.nama_layer_2 || '',
                nama_layer_3: data.nama_layer_3 || '',
                nama_layer_4: data.nama_layer_4 || ''
            });

            function doIntervensiFetch() {
                fetch(modalApiUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(function(res) {
                        if (!res || !res.success) throw new Error('Request failed');

                        // Hanya tampilkan intervensi untuk Layer 1 di IPK-IKK & OKK.
                        // Sumber penentuan Layer 1 mengacu pada kolom employee_type (ikk_work_permit_employee).
                        var ipkAll = res.ipk_ikk || [];
                        var okkAll = res.okk || [];
                        var oak = res.oak || [];

                        function isLayer1Row(row) {
                            // Prioritas utama: employee_type dari ikk_work_permit_employee (jika tersedia di payload).
                            var raw = (row.employee_type !== undefined && row.employee_type !== null && row.employee_type !== '')
                                ? row.employee_type
                                // Fallback lama: layer_pengawas kalau tidak ada employee_type.
                                : (row.layer_pengawas || '');

                            var lv = raw.toString().trim().toLowerCase();
                            return lv === 'layer 1' || lv === 'layer1' || lv === '1';
                        }

                        var ipk = ipkAll.filter(function (r) {
                            // Jika tidak ada info layer sama sekali, biarkan lewat supaya tidak menghilangkan data lama.
                            if (r.employee_type === undefined && r.layer_pengawas === undefined) return true;
                            return isLayer1Row(r);
                        });
                        var okk = okkAll.filter(function (r) {
                            if (r.employee_type === undefined && r.layer_pengawas === undefined) return true;
                            return isLayer1Row(r);
                        });
                        document.getElementById('intervensiBadgeIpk').textContent = ipk.length;
                        document.getElementById('intervensiBadgeOkk').textContent = okk.length;
                        document.getElementById('intervensiBadgeOak').textContent = oak.length;
                        document.getElementById('intervensiIpkLoading').classList.add('d-none');
                        document.getElementById('intervensiOkkLoading').classList.add('d-none');
                        document.getElementById('intervensiOakLoading').classList.add('d-none');
                        if (ipk.length === 0) { document.getElementById('intervensiIpkEmpty').classList.remove('d-none'); document.getElementById('intervensiIpkTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiIpkEmpty').classList.add('d-none');
                            document.getElementById('intervensiIpkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableIpk tbody');
                            if (tbody) { tbody.innerHTML = ''; ipk.forEach(function(r) {
                                tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.durasi_jam), safeStr(r.cctv_terekam), safeStr(r.kategori_ijk, 35), safeStr(r.status_pekerjaan)]));
                            }); }
                        }
                        if (okk.length === 0) { document.getElementById('intervensiOkkEmpty').classList.remove('d-none'); document.getElementById('intervensiOkkTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiOkkEmpty').classList.add('d-none');
                            document.getElementById('intervensiOkkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOkk tbody');
                            if (tbody) { tbody.innerHTML = ''; okk.forEach(function(r) {
                                tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.jenis_ijk, 35), safeStr(r.layer_pengawas)]));
                            }); }
                        }
                        if (oak.length === 0) { document.getElementById('intervensiOakEmpty').classList.remove('d-none'); document.getElementById('intervensiOakTableWrap').classList.add('d-none'); } else {
                            document.getElementById('intervensiOakEmpty').classList.add('d-none');
                            document.getElementById('intervensiOakTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOak tbody');
                            if (tbody) { tbody.innerHTML = ''; oak.forEach(function(r) {
                                tbody.appendChild(tr([safeStr(r.activity), safeStr(r.sub_activity), safeStr(r.submit_date), safeStr(r.submit_by), safeStr(r.kode_sid_pelapor), safeStr(r.location), safeStr(r.detail_location), safeStr(r.conclusion, 50), safeStr(r.site)]));
                            }); }
                        }
                    })
                    .catch(function(err) {
                        document.getElementById('intervensiIpkLoading').classList.add('d-none');
                        document.getElementById('intervensiOkkLoading').classList.add('d-none');
                        document.getElementById('intervensiOakLoading').classList.add('d-none');
                        document.getElementById('intervensiIpkEmpty').classList.remove('d-none');
                        document.getElementById('intervensiOkkEmpty').classList.remove('d-none');
                        document.getElementById('intervensiOakEmpty').classList.remove('d-none');
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal memuat', text: err.message || 'Gagal memuat data.' });
                    });
            }

            function doLayer1Fetch() {
                if (!hasLayer1) return;
            var layer1UsersEl2 = document.getElementById('intervensiLayer1Users');
            var layer1EmptyEl2 = document.getElementById('intervensiLayer1Empty');
            var layer1LoadingEl2 = document.getElementById('intervensiLayer1Loading');
            var layer1SelectEl2 = document.getElementById('intervensiLayer1Select');
            var layer1BtnEl2 = document.getElementById('intervensiLayer1Btn');
            var okkLayer1UsersEl2 = document.getElementById('intervensiOkkLayer1Users');
            var okkLayer1EmptyEl2 = document.getElementById('intervensiOkkLayer1Empty');
            var okkLayer1LoadingEl2 = document.getElementById('intervensiOkkLayer1Loading');
            var okkLayer1SelectEl2 = document.getElementById('intervensiOkkLayer1Select');
            var okkLayer1BtnEl2 = document.getElementById('intervensiOkkLayer1Btn');
            layer1LoadingEl2.classList.remove('d-none');
            layer1UsersEl2.innerHTML = '';
            layer1EmptyEl2.classList.add('d-none');
            if (layer1SelectEl2) { layer1SelectEl2.classList.add('d-none'); layer1SelectEl2.innerHTML = '<option value=\"\">Pilih PIC Layer 1...</option>'; }
            if (layer1BtnEl2) { layer1BtnEl2.classList.add('d-none'); layer1BtnEl2.removeAttribute('data-message'); }
            if (okkLayer1LoadingEl2) {
                okkLayer1LoadingEl2.classList.remove('d-none');
                okkLayer1UsersEl2.innerHTML = '';
                okkLayer1EmptyEl2.classList.add('d-none');
                if (okkLayer1SelectEl2) { okkLayer1SelectEl2.classList.add('d-none'); okkLayer1SelectEl2.innerHTML = '<option value=\"\">Pilih PIC Layer 1...</option>'; }
                if (okkLayer1BtnEl2) { okkLayer1BtnEl2.classList.add('d-none'); okkLayer1BtnEl2.removeAttribute('data-message'); }
            }
                var qs = new URLSearchParams();
                if (sidLayer1) qs.set('sid_layer_1', sidLayer1);
                if (namaLayer1) qs.set('nama_layer_1', namaLayer1);
                fetch(layer1UsersApiUrl + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        layer1LoadingEl2.classList.add('d-none');
                        if (okkLayer1LoadingEl2) okkLayer1LoadingEl2.classList.add('d-none');
                        var usersRaw = (res && res.success && res.users) ? res.users : [];
                        // Deduplikasi users: berdasarkan ID (jika ada), atau kombinasi nomor WA + nama + username
                        var seen = {};
                        var users = [];
                        usersRaw.forEach(function(u) {
                            var key = null;
                            if (u.id) {
                                key = 'id_' + u.id;
                            } else {
                                var num = normalizeWaNumber(u.selular);
                                var nama = (u.nama || '').trim();
                                var username = (u.username || '').trim();
                                key = 'wa_' + (num || '') + '_n_' + nama + '_u_' + username;
                            }
                            if (key && !seen[key]) {
                                seen[key] = true;
                                users.push(u);
                            }
                        });
                        var displayName = (res && res.nama_layer_1) ? res.nama_layer_1 : namaLayer1;
                        document.getElementById('intervensiLayer1NameDisplay').textContent = displayName || '—';
                        if (document.getElementById('intervensiOkkLayer1NameDisplay')) document.getElementById('intervensiOkkLayer1NameDisplay').textContent = displayName || '—';
                        var ipkMsg = (displayName || 'PIC') + ', anda harus mengisi INSPEKSI PRA KERJA (IPK) untuk pekerjaan berikut:\n\n'
                            + 'IKK: ' + (data.kode_ikk || '—') + (data.nama_pekerjaan ? ' - ' + data.nama_pekerjaan : '') + '\n'
                            + 'Hari: ' + (data.tanggal_dop || '—') + '\n'
                            + 'Lokasi: ' + (data.location_name || '—') + '\n'
                            + 'Detail Lokasi: ' + (data.location_detail_name || '—') + '\n'
                            + 'Layer 1: ' + (data.nama_layer_1 || '—') + '\n'
                            + 'Layer 2: ' + (data.nama_layer_2 || '—') + '\n'
                            + 'Layer 3: ' + (data.nama_layer_3 || '—') + '\n'
                            + 'Layer 4: ' + (data.nama_layer_4 || '—') + '\n\n'
                            + ipkFormLink;
                        var okkMsg = (displayName || 'PIC') + ', mohon perhatian untuk OBSERVASI KEGIATAN KERJA (OKK).\n\n'
                            + 'IKK: ' + (data.kode_ikk || '—') + (data.nama_pekerjaan ? ' - ' + data.nama_pekerjaan : '') + '\n'
                            + 'Hari: ' + (data.tanggal_dop || '—') + '\n'
                            + 'Lokasi: ' + (data.location_name || '—') + '\n'
                            + 'Detail Lokasi: ' + (data.location_detail_name || '—') + '\n'
                            + 'Layer 1: ' + (data.nama_layer_1 || '—') + '\n'
                            + 'Layer 2: ' + (data.nama_layer_2 || '—') + '\n'
                            + 'Layer 3: ' + (data.nama_layer_3 || '—') + '\n'
                            + 'Layer 4: ' + (data.nama_layer_4 || '—');

                        if (users.length === 0) {
                            layer1EmptyEl2.classList.remove('d-none');
                            if (okkLayer1EmptyEl2) okkLayer1EmptyEl2.classList.remove('d-none');
                            return;
                        }

                        // Isi dropdown IPK
                        if (layer1SelectEl2) {
                            users.forEach(function (u) {
                                var num = normalizeWaNumber(u.selular);
                                var label = u.nama || u.username || 'User';
                                if (!num) return;
                                var opt = document.createElement('option');
                                opt.value = num;
                                opt.textContent = label + ' (' + num + ')';
                                opt.setAttribute('data-label', label);
                                layer1SelectEl2.appendChild(opt);
                            });
                            if (layer1SelectEl2.options.length > 1) {
                                layer1SelectEl2.classList.remove('d-none');
                                if (layer1BtnEl2) {
                                    layer1BtnEl2.classList.remove('d-none');
                                    layer1BtnEl2.dataset.message = ipkMsg;
                                    if (!layer1BtnEl2._bound) {
                                        layer1BtnEl2.addEventListener('click', function () {
                                            var sel = layer1SelectEl2;
                                            if (!sel || sel.value === '') return;
                                            var num = sel.value;
                                            var msg = layer1BtnEl2.dataset.message || ipkMsg;
                                            window.open('https://wa.me/' + num + '?text=' + encodeURIComponent(msg), '_blank');
                                        });
                                        layer1BtnEl2._bound = true;
                                    }
                                }
                            } else {
                                layer1EmptyEl2.classList.remove('d-none');
                            }
                        }

                        // Isi dropdown OKK (pakai list user yang sama)
                        if (okkLayer1SelectEl2) {
                            users.forEach(function (u) {
                                var num = normalizeWaNumber(u.selular);
                                var label = u.nama || u.username || 'User';
                                if (!num) return;
                                var opt = document.createElement('option');
                                opt.value = num;
                                opt.textContent = label + ' (' + num + ')';
                                opt.setAttribute('data-label', label);
                                okkLayer1SelectEl2.appendChild(opt);
                            });
                            if (okkLayer1SelectEl2.options.length > 1) {
                                okkLayer1SelectEl2.classList.remove('d-none');
                                if (okkLayer1BtnEl2) {
                                    okkLayer1BtnEl2.classList.remove('d-none');
                                    okkLayer1BtnEl2.dataset.message = okkMsg;
                                    if (!okkLayer1BtnEl2._bound) {
                                        okkLayer1BtnEl2.addEventListener('click', function () {
                                            var sel = okkLayer1SelectEl2;
                                            if (!sel || sel.value === '') return;
                                            var num = sel.value;
                                            var msg = okkLayer1BtnEl2.dataset.message || okkMsg;
                                            window.open('https://wa.me/' + num + '?text=' + encodeURIComponent(msg), '_blank');
                                        });
                                        okkLayer1BtnEl2._bound = true;
                                    }
                                }
                            } else if (okkLayer1EmptyEl2) {
                                okkLayer1EmptyEl2.classList.remove('d-none');
                            }
                        }
                    })
                    .catch(function() {
                        layer1LoadingEl2.classList.add('d-none');
                        layer1EmptyEl2.classList.remove('d-none');
                        if (okkLayer1LoadingEl2) { okkLayer1LoadingEl2.classList.add('d-none'); if (okkLayer1EmptyEl2) okkLayer1EmptyEl2.classList.remove('d-none'); }
                    });
            }

            function doOakLayers234Fetch() {
                var qs = new URLSearchParams();
                qs.set('sid_layer_2', data.sid_layer_2 || '');
                qs.set('sid_layer_3', data.sid_layer_3 || '');
                qs.set('sid_layer_4', data.sid_layer_4 || '');
                qs.set('nama_layer_2', data.nama_layer_2 || '');
                qs.set('nama_layer_3', data.nama_layer_3 || '');
                qs.set('nama_layer_4', data.nama_layer_4 || '');
                fetch(layers234UsersApiUrl + '?' + qs.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        [2, 3, 4].forEach(function(n) {
                            var key = 'layer_' + n;
                            var usersEl = document.getElementById('intervensiOakLayer' + n + 'Users');
                            var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                            var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                            var nameEl = document.getElementById('intervensiOakLayer' + n + 'Name');
                            if (loadingEl) loadingEl.classList.add('d-none');
                            var layerData = res && res[key] ? res[key] : { users: [], nama_layer: '' };
                            var usersRaw = layerData.users || [];
                            // Deduplikasi users: berdasarkan ID (jika ada), atau kombinasi nomor WA + nama + username
                            var seen = {};
                            var users = [];
                            usersRaw.forEach(function(u) {
                                var key = null;
                                if (u.id) {
                                    key = 'id_' + u.id;
                                } else {
                                    var num = normalizeWaNumber(u.selular);
                                    var nama = (u.nama || '').trim();
                                    var username = (u.username || '').trim();
                                    key = 'wa_' + (num || '') + '_n_' + nama + '_u_' + username;
                                }
                                if (key && !seen[key]) {
                                    seen[key] = true;
                                    users.push(u);
                                }
                            });
                            var displayName = layerData.nama_layer || '—';
                            if (nameEl) nameEl.textContent = displayName;
                            if (!usersEl) return;
                            usersEl.innerHTML = '';
                            var oakMsg = (displayName !== '—' ? displayName : 'PIC') + ', mohon perhatian untuk OAK (Observasi Aktivitas Kerja) sesuai IKK ini.';
                            users.forEach(function(u) {
                                var num = normalizeWaNumber(u.selular);
                                if (!num) return;
                                var label = u.nama || u.username || 'User';
                                var a = document.createElement('a');
                                a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(oakMsg);
                                a.target = '_blank';
                                a.rel = 'noopener';
                                a.className = 'btn btn-sm btn-warning text-dark';
                                a.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:14px;">send</i> Intervensi by WA';
                                a.title = label;
                                usersEl.appendChild(a);
                            });
                            if (users.length === 0 && emptyEl) emptyEl.classList.remove('d-none');
                        });
                    })
                    .catch(function() {
                        [2, 3, 4].forEach(function(n) {
                            var loadingEl = document.getElementById('intervensiOakLayer' + n + 'Loading');
                            var emptyEl = document.getElementById('intervensiOakLayer' + n + 'Empty');
                            if (loadingEl) loadingEl.classList.add('d-none');
                            if (emptyEl) emptyEl.classList.remove('d-none');
                        });
                    });
            }

            doIntervensiFetch();
            doLayer1Fetch();
            doOakLayers234Fetch();
            return;
        }

        var btn = e.target.closest('.btn-detail-dopm') || (e.target.closest('.dopm-matriks-row') && !e.target.closest('.btn-intervensi-dopm') ? e.target.closest('.dopm-matriks-row') : null);
        if (!btn) return;
        var data = JSON.parse(btn.getAttribute('data-dopm') || '{}');
        window._lastDetailDopmData = data;
        var modalDoc = document.getElementById('detailDopmModal');
        document.getElementById('detailDopmTitle').textContent = (data.id_dop || 'Detail') + ' — ' + (data.nama_pekerjaan || 'DOPM').substring(0, 50);
        document.getElementById('detailDopmSubtitle').textContent = 'Kode IKK: ' + (data.kode_ikk || '—');
        function dash(val) { return (val == null || val === '' || val === undefined) ? '—' : String(val); }
        document.getElementById('detailDopmId').textContent = dash(data.id_dop);
        document.getElementById('detailDopmKodeIkk').textContent = dash(data.kode_ikk);
        document.getElementById('detailDopmNamaPekerjaan').textContent = dash(data.nama_pekerjaan);
        document.getElementById('detailDopmSite').textContent = dash(data.site_ijin_kerja_khusus);
        document.getElementById('detailDopmPerusahaan').textContent = dash(data.perusahaan_ijin_kerja_khusus);
        document.getElementById('detailDopmJenisIjk').textContent = dash(data.jenis_ijin_kerja_khusus);
        document.getElementById('detailDopmTanggal').textContent = dash(data.tanggal_dop);
        document.getElementById('detailDopmStatus').textContent = dash(data.status);
        document.getElementById('detailDopmLocation').textContent = dash(data.location_name);
        document.getElementById('detailDopmLocationDetail').textContent = dash(data.location_detail_name);
        document.getElementById('detailDopmLayer1').textContent = dash(data.nama_layer_1);
        document.getElementById('detailDopmLayer2').textContent = dash(data.nama_layer_2);
        document.getElementById('detailDopmLayer3').textContent = dash(data.nama_layer_3);
        document.getElementById('detailDopmLayer4').textContent = dash(data.nama_layer_4);
        document.getElementById('badgeIpkIkk').textContent = '0';
        document.getElementById('badgeOkk').textContent = '0';
        document.getElementById('badgeOak').textContent = '0';
        document.getElementById('statCountIpkIkk').textContent = '0';
        document.getElementById('statCountOkk').textContent = '0';
        document.getElementById('statCountOak').textContent = '0';
        showLoading('ipkIkk');
        showLoading('okk');
        showLoading('oak');
        document.getElementById('oakContext').classList.add('d-none');
        if (modal) modal.show();
        var params = new URLSearchParams({
            kode_ikk: data.kode_ikk || '',
            work_permit_id: data.work_permit_id || '',
            jenis_ijin_kerja_khusus: data.jenis_ijin_kerja_khusus || '',
            sid_layer_2: data.sid_layer_2 || '',
            sid_layer_3: data.sid_layer_3 || '',
            sid_layer_4: data.sid_layer_4 || '',
            nama_layer_2: data.nama_layer_2 || '',
            nama_layer_3: data.nama_layer_3 || '',
            nama_layer_4: data.nama_layer_4 || '',
            location_name: data.location_name || '',
            location_detail_name: data.location_detail_name || '',
            tanggal_dop: data.tanggal_dop || ''
        });
        console.log('[OAK] Modal request params (data-dopm):', { location_name: data.location_name, location_detail_name: data.location_detail_name, tanggal_dop: data.tanggal_dop, kode_ikk: data.kode_ikk, work_permit_id: data.work_permit_id });
        console.log('[OAK] Modal API URL params:', params.toString());
        function doFetch() {
        fetch(modalApiUrl + '?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function(r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                var ct = (r.headers.get('Content-Type') || '').toLowerCase();
                if (ct.indexOf('application/json') === -1) throw new Error('Response bukan JSON');
                return r.json();
            })
            .then(function(res) {
                if (!res || !res.success) throw new Error(res && res.message ? res.message : 'Request failed');
                var ipk = res.ipk_ikk || [];
                var okk = res.okk || [];
                var oak = res.oak || [];
                var ipkSource = res.ipk_source || 'mysql';
                var okkSource = res.okk_source || 'mysql';
                console.log('[OAK] API response:', { oakCount: oak.length, oak: oak, fullResKeys: res ? Object.keys(res) : [], ipk_source: ipkSource, okk_source: okkSource });
                if (oak.length > 0 && oak[0]) console.log('[OAK] First OAK row keys:', Object.keys(oak[0]), 'sample:', oak[0]);
                var ctx = res.dopm_context || {};
                var layerNames = [ctx.nama_layer_2, ctx.nama_layer_3, ctx.nama_layer_4].filter(Boolean).join(' / ') || '—';
                document.getElementById('oakLayerNames').textContent = layerNames;
                document.getElementById('badgeIpkIkk').textContent = ipk.length;
                document.getElementById('badgeOkk').textContent = okk.length;
                var ipkSourceEl = document.getElementById('ipkIkkSourceLabel');
                var okkSourceEl = document.getElementById('okkSourceLabel');
                if (ipkSourceEl) ipkSourceEl.textContent = ipkSource === 'clickhouse' ? ' (Sumber: ClickHouse)' : ' (Sumber: MySQL)';
                if (okkSourceEl) okkSourceEl.textContent = okkSource === 'clickhouse' ? ' (Sumber: ClickHouse)' : ' (Sumber: MySQL)';
                document.getElementById('badgeOak').textContent = oak.length;
                document.getElementById('statCountIpkIkk').textContent = ipk.length;
                document.getElementById('statCountOkk').textContent = okk.length;
                document.getElementById('statCountOak').textContent = oak.length;
                if (layerNames !== '—') document.getElementById('oakContext').classList.remove('d-none');
                if (res.location_name != null && res.location_name !== '') document.getElementById('detailDopmLocation').textContent = res.location_name;
                if (res.location_detail_name != null && res.location_detail_name !== '') document.getElementById('detailDopmLocationDetail').textContent = res.location_detail_name;

                // Tabel IPK-IKK (pastikan ambil elemen di dalam modal)
                var tbodyIpk = modalDoc.querySelector('#tableIpkIkk tbody');
                if (tbodyIpk) {
                    tbodyIpk.innerHTML = '';
                    if (ipk.length === 0) {
                        showEmpty('ipkIkk');
                    } else {
                        showTable('ipkIkk');
                        ipk.forEach(function(r) {
                            tbodyIpk.appendChild(tr([
                                formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk),
                                safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.durasi_jam),
                                safeStr(r.cctv_terekam), safeStr(r.kategori_ijk, 35), safeStr(r.status_pekerjaan)
                            ]));
                        });
                    }
                }

                // Tabel OKK
                var tbodyOkk = modalDoc.querySelector('#tableOkk tbody');
                if (tbodyOkk) {
                    tbodyOkk.innerHTML = '';
                    if (okk.length === 0) {
                        showEmpty('okk');
                    } else {
                        showTable('okk');
                        okk.forEach(function(r) {
                            tbodyOkk.appendChild(tr([
                                formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk),
                                safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.jenis_ijk, 35), safeStr(r.layer_pengawas)
                            ]));
                        });
                    }
                }

                // Tabel OAK
                var tbodyOak = modalDoc.querySelector('#tableOak tbody');
                if (tbodyOak) {
                    tbodyOak.innerHTML = '';
                    if (oak.length === 0) {
                        showEmpty('oak');
                    } else {
                        showTable('oak');
                        oak.forEach(function(r) {
                            tbodyOak.appendChild(tr([
                                safeStr(r.activity), safeStr(r.sub_activity), safeStr(r.submit_date), safeStr(r.submit_by),
                                safeStr(r.kode_sid_pelapor), safeStr(r.location), safeStr(r.detail_location), safeStr(r.conclusion, 50), safeStr(r.site)
                            ]));
                        });
                    }
                }
            })
            .catch(function(err) {
                showEmpty('ipkIkk');
                showEmpty('okk');
                showEmpty('oak');
                console.error(err);
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Gagal memuat', text: err.message || 'Gagal memuat data detail.' });
            });
        }
        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function onShown() {
                modalEl.removeEventListener('shown.bs.modal', onShown);
                doFetch();
            }, { once: true });
        }
    });

    // Tombol Intervensi (footer + di dalam modal): tutup detail, buka modal Intervensi dengan data yang sama
    function openIntervensiFromDetail() {
        var data = window._lastDetailDopmData;
        if (!data || !intervensiModal) return;
        if (modal && modal.hide) modal.hide();
        var fake = document.createElement('button');
        fake.className = 'btn-intervensi-dopm d-none';
        fake.setAttribute('data-dopm', JSON.stringify(data));
        document.body.appendChild(fake);
        fake.click();
        fake.remove();
    }
    [document.getElementById('btnIntervensiFromDetail'), document.getElementById('btnIntervensiFromDetailTop')].forEach(function(btn) {
        if (btn && intervensiModal) btn.addEventListener('click', openIntervensiFromDetail);
    });

    // Klik Enter pada baris matriks (Need Action / Warning / Complete) buka detail
    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        var row = e.target.closest('.dopm-matriks-row');
        if (!row || e.target.closest('.btn-intervensi-dopm')) return;
        e.preventDefault();
        row.click();
    });
})();
</script>
@endsection



