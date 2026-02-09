@extends('layouts.masterMotionHazardAdmin')

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
    #detailDopmModal .nav-pills { background: #fff; }
    #detailDopmModal .nav-pills .nav-link { color: #4b5563; background: #f9fafb; border: 1px solid #e5e7eb; }
    #detailDopmModal .nav-pills .nav-link:hover { background: #f3f4f6; color: #111827; }
    #detailDopmModal .nav-pills .nav-link.active { background: #fff; color: #111827; border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.25); }
    #detailDopmModal .modal-tab-table-scroll { max-height: 360px; overflow-y: auto; min-height: 120px; }
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
#detailDopmModal .tab-pane { min-height: 260px; }
#detailDopmModal .tab-pane.active { display: block !important; }
#detailDopmModal #tableIpkIkk,
#detailDopmModal #tableOkk,
#detailDopmModal #tableOak { width: 100%; background: #fff; }
    #intervensiDopmModal .modal-body .tab-content { min-height: 300px; }
    #intervensiDopmModal .tab-pane { min-height: 260px; }
    #tableDopmHarian thead th { white-space: nowrap; }
    
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
    <h1 class="hazard-detection-title">DOPM & IKK - Dashboard</h1>
    <p class="hazard-detection-subtitle">Statistik harian DOPM, IPK-IKK, OKK, dan OAK (Observasi Area Kerja)</p>

    {{-- Filter tanggal --}}
    <div class="card rounded-4 mb-3">
        <div class="card-body py-3">
            <form method="get" action="{{ route('dopmikk.dopm.dashboard') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label for="filterDate" class="form-label mb-0 small fw-semibold">Tampilkan data tanggal</label>
                    <input type="date" name="date" id="filterDate" class="form-control" value="{{ $filterDate ?? now()->toDateString() }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary rounded-3">
                        <i class="material-icons-outlined me-1" style="font-size: 18px;">search</i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-3">
        <button class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#dashboardStatsCollapse" aria-expanded="true" aria-controls="dashboardStatsCollapse">
            <span class="fw-bold d-flex align-items-center">
                <i class="material-icons-outlined me-2">dashboard</i>
                Statistik DOPM, IKK, OKK & OAK
            </span>
            <i class="material-icons-outlined collapse-icon">expand_less</i>
        </button>
    </div>

    <div class="collapse show" id="dashboardStatsCollapse">
        <div class="row">
            <div class="col-12 d-flex">
                <div class="card rounded-4 w-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">Ringkasan Data Harian</h5>
                            <span class="badge bg-primary">{{ \Carbon\Carbon::parse($filterDate ?? now())->locale('id')->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-around flex-wrap gap-4 p-4">
                            <a href="{{ route('dopmikk.dopm.index') }}" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Data DOPM tanggal terpilih">
                                <span class="mb-2 wh-48 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">assignment</i>
                                </span>
                                <h3 class="mb-0">{{ number_format($totalDopmHarian ?? 0) }}</h3>
                                <p class="mb-0">DOPM</p>
                                <small class="text-muted">Data Hari ini</small>
                            </a>
                            <div class="vr"></div>
                            <a href="{{ route('dopmikk.ipk-ikk.index') }}" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Presentase IKK yang ada IPK ({{ $ikkAdaIpkCount ?? 0 }}/{{ $totalIkkUnikHarian ?? 0 }} IKK)">
                                <span class="mb-2 wh-48 bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">checklist</i>
                                </span>
                                <h3 class="mb-0">{{ $pctIkkAdaIpk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada IPK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaIpkCount ?? 0) }} belum IPK-IKK</small>
                            </a>
                            <div class="vr"></div>
                            <a href="{{ route('dopmikk.okk.index') }}" class="btn p-0 border-0 bg-transparent d-flex flex-column align-items-center justify-content-center gap-2 text-decoration-none text-dark" title="Presentase IKK yang ada OKK ({{ $ikkAdaOkkCount ?? 0 }}/{{ $totalIkkUnikHarian ?? 0 }} IKK)">
                                <span class="mb-2 wh-48 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">folder_open</i>
                                </span>
                                <h3 class="mb-0">{{ $pctIkkAdaOkk ?? 0 }}%</h3>
                                <p class="mb-0">IKK ada OKK</p>
                                <small class="text-muted">{{ ($totalIkkUnikHarian ?? 0) - ($ikkAdaOkkCount ?? 0) }} belum OKK</small>
                            </a>
                            <div class="vr"></div>
                            <div class="d-flex flex-column align-items-center justify-content-center gap-2" title="OAK (Observasi Area Kerja) tanggal terpilih">
                                <span class="mb-2 wh-48 bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="material-icons-outlined">visibility</i>
                                </span>
                                <h3 class="mb-0">{{ isset($totalOakHarian) && $totalOakHarian !== null ? number_format($totalOakHarian) : '—' }}</h3>
                                <p class="mb-0">OAK</p>
                                <small class="text-muted">Data Hari ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data DOPM harian (tampil langsung) --}}
        <div class="row mt-3">
            <div class="col-12">
                <div class="card rounded-4 border-0 shadow-sm">
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
                </div>
            </div>
        </div>

        <div class="row">
    
          <div class="col-12 col-xl-5 col-xxl-4 d-flex">
            <div class="card rounded-4 w-100 shadow-none bg-transparent border-0">
               <div class="card-body p-0">
                 <div class="row g-4">
                    

              
                    <div class="col-12 col-xl-12">
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <h5 class="fw-bold mb-1">Akses Cepat Modul DOPM & IKK</h5>
                                    <small class="text-muted">Navigasi ke data DOPM, IPK-IKK, OKK</small>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-0">
                                    <a href="{{ route('dopmikk.dopm.index') }}" class="btn btn-primary rounded-3"><i class="material-icons-outlined me-1" style="font-size: 18px;">assignment</i> Data DOPM</a>
                                    <a href="{{ route('dopmikk.ipk-ikk.index') }}" class="btn btn-info rounded-3"><i class="material-icons-outlined me-1" style="font-size: 18px;">checklist</i> Data IPK-IKK</a>
                                    <a href="{{ route('dopmikk.okk.index') }}" class="btn btn-success rounded-3"><i class="material-icons-outlined me-1" style="font-size: 18px;">folder_open</i> Data OKK</a>
                                </div>
                                <p class="text-muted small mt-3 mb-0">OAK (Observasi Area Kerja) dari sistem eksternal; detail di Smart Alert Maps.</p>
                                <div class="accordion d-none" id="accordionAreaKritis">
                                    <!-- Jumlah Area Kritis -->
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseJumlahAreaKritis">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                                        <span class="material-icons-outlined text-danger">warning</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="mb-0 fw-bold" id="modalJumlahAreaKritis">0</h4>
                                                        <small class="text-muted">Jumlah Area Kritis</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseJumlahAreaKritis" class="accordion-collapse collapse" data-bs-parent="#accordionAreaKritis">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <strong>Penjelasan:</strong> Menampilkan total jumlah area yang dikategorikan sebagai area kritis. 
                                                    Area kritis adalah lokasi yang memiliki potensi bahaya tinggi dan memerlukan monitoring khusus 
                                                    untuk mencegah terjadinya insiden keselamatan kerja.
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total Area Kritis:</span>
                                                            <span class="badge bg-danger fs-6" id="detailJumlahAreaKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total Area Non Kritis:</span>
                                                            <span class="badge bg-success fs-6" id="detailJumlahAreaNonKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total Area:</span>
                                                            <span class="badge bg-primary fs-6" id="detailTotalArea">0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3" id="listDetailAreaKritis">
                                                    <strong>Detail Area Kritis:</strong>
                                                    <div class="mt-2">
                                                        <small class="text-muted">Memuat data...</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Aktivitas Highrisk -->
                                    <div class="accordion-item mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCctvAreaKritis">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                                        <span class="material-icons-outlined text-warning">warning</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="mb-0 fw-bold" id="modalCctvAreaKritis">0</h4>
                                                        <small class="text-muted">Aktivitas Highrisk</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseCctvAreaKritis" class="accordion-collapse collapse" data-bs-parent="#accordionAreaKritis">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <strong>Penjelasan:</strong> Menampilkan jumlah aktivitas highrisk yang tercatat di tabel cctv_coverage. 
                                                    Aktivitas highrisk adalah aktivitas dengan tingkat risiko tinggi yang memerlukan monitoring khusus 
                                                    untuk mencegah terjadinya insiden keselamatan kerja.
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Aktivitas Highrisk:</span>
                                                            <span class="badge bg-warning fs-6" id="detailCctvAreaKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total CCTV:</span>
                                                            <span class="badge bg-primary fs-6" id="detailTotalCctvAreaKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Persentase Aktivitas Highrisk:</span>
                                                            <span class="badge bg-info fs-6" id="detailPersentaseCctvAreaKritis">0%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3" id="listDetailAktivitasHighrisk">
                                                    <strong>Detail Lokasi Aktivitas Highrisk:</strong>
                                                    <div class="mt-2">
                                                        <small class="text-muted">Memuat data...</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- CCTV Area Non Kritis -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCctvAreaNonKritis">
                                                <div class="d-flex align-items-center gap-3 w-100">
                                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                                        <span class="material-icons-outlined text-success">check_circle</span>
                                                    </div>
                                                    <div>
                                                        <h4 class="mb-0 fw-bold" id="modalCctvAreaNonKritis">0</h4>
                                                        <small class="text-muted">CCTV Area Non Kritis</small>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapseCctvAreaNonKritis" class="accordion-collapse collapse" data-bs-parent="#accordionAreaKritis">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <strong>Penjelasan:</strong> Menampilkan jumlah kamera CCTV yang terpasang di area non kritis. 
                                                    Area non kritis adalah lokasi dengan tingkat risiko rendah yang tetap dipantau untuk 
                                                    keamanan umum dan operasional sehari-hari.
                                                </div>
                                                <div class="row g-3 mt-2">
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">CCTV di Area Non Kritis:</span>
                                                            <span class="badge bg-success fs-6" id="detailCctvAreaNonKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Total CCTV:</span>
                                                            <span class="badge bg-primary fs-6" id="detailTotalCctvAreaNonKritis">0</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                            <span class="fw-semibold">Persentase Coverage Area Non Kritis:</span>
                                                            <span class="badge bg-info fs-6" id="detailPersentaseCctvAreaNonKritis">0%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                 
                 
                  
                   <div class="col-12 d-flex">
                      <div class="card mb-0 rounded-4 w-100">
                       <div class="card-body">
                         <h5 class="fw-bold mb-2">Ringkasan</h5>
                         <p class="text-muted mb-0">Dashboard ini menampilkan total rekor DOPM, IPK-IKK, OKK, dan OAK. Gunakan tombol di atas untuk membuka data masing-masing modul.</p>
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
                    <h5 class="mb-0 fw-bold">DOPM, IKK, OKK & OAK</h5>
                    <small class="text-muted">Statistik dan akses data modul DOPM$IKK</small>
                  </div>
                 </div>
                 {{-- <div class="table-responsive">
                   <table class="table table-hover align-middle mb-4" id="coverageTable">
                     <tbody id="coverageTableBody">
                       <tr>
                         <td colspan="5" class="text-center text-muted py-4">
                           <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                           Memuat data...
                         </td>
                       </tr>
                     </tbody>
                   </table>
                 </div> --}}
                  {{-- <div class="d-flex flex-column flex-lg-row align-items-start justify-content-around border p-3 rounded-4 mt-3 gap-3">
                   
                    <div class="d-flex align-items-center gap-4 cctv-stat-card" id="cctvStatCard" style="cursor: pointer; padding: 8px; border-radius: 8px; transition: all 0.2s;" 
                         title="Klik untuk melihat detail TBC">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutCctv"
                            data-peity='{ "fill": ["#6f42c1", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">TBC</p>
                        <h2 class="mb-0" id="statCctvCount">0</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statCctvChange">0 %</span><span id="statCctvText"> valid TBC</span></p>
                      </div>
                    </div>
                     <div class="vr"></div>
                    <div class="d-flex align-items-center gap-4">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutHazard"
                            data-peity='{ "fill": ["#0d6efd", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">HAZARD</p>
                        <h2 class="mb-0" id="statHazardCount">{{ number_format($monthlyHazards ?? 65) }}</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statHazardChange">{{ $monthlyChange ?? '16.5' }}%</span><span id="statHazardText">{{ $monthlyCount ?? '55' }} hazards</span></p>
                      </div>
                    </div>
                   
                    

                      <div class="vr"></div>
                    <div class="d-flex align-items-center gap-4">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutInsiden"
                            data-peity='{ "fill": ["#fd7e14", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">INSIDEN</p>
                        <h2 class="mb-0" id="statInsidenCount">{{ number_format($yearlyHazards ?? 9) }}</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statInsidenChange">{{ $yearlyChange ?? '24.9' }}%</span><span id="statInsidenText">{{ $yearlyCount ?? '267' }} hazards</span></p>
                      </div>
                    </div>

                      <div class="vr"></div>
                    <div class="d-flex align-items-center gap-4">
                      <div class="">
                        <p class="mb-0 data-attributes">
                          <span id="donutGr"
                            data-peity='{ "fill": ["#20c997", "rgb(0 0 0 / 10%)"], "innerRadius": 32, "radius": 40 }'>0/100</span>
                        </p>
                      </div>
                      <div class="">
                        <p class="mb-1 fs-6 fw-bold">GR</p>
                        <h2 class="mb-0" id="statGrCount">{{ number_format($validGrCount ?? 0) }}</h2>
                        <p class="mb-0"><span class="text-success me-2 fw-medium" id="statGrChange">{{ $yearlyChange ?? '24.9' }}%</span><span id="statGrText">{{ $yearlyCount ?? '267' }} hazards</span></p>
                      </div>
                    </div>

                    
                  </div> --}}

                 
                  <div class="border p-3 rounded-4 mt-3">
                    <p class="mb-0 text-muted">Gunakan kartu statistik di atas untuk melihat total DOPM, IPK-IKK, OKK, dan OAK. Klik setiap kartu untuk membuka daftar data.</p>
                  </div>

                    
                  </div>
               </div>
            </div>  
          </div> 
</div><!--end row-->
    </div><!--end collapse-->





</div>

    {{-- Modal Detail DOPM: 3 tab IPK-IKK, OKK, OAK (Bootstrap modal full) --}}
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
                    <p class="small fw-semibold text-muted px-4 pt-2 mb-0">Detail Data</p>
                    <ul class="nav nav-pills nav-fill px-3 pt-2 pb-0 gap-2 border-bottom rounded-0" id="detailDopmTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 fw-semibold" id="tab-ipk-ikk" data-bs-toggle="tab" data-bs-target="#panel-ipk-ikk" type="button" role="tab">
                                <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">checklist</i> IPK-IKK <span class="badge bg-primary ms-1" id="badgeIpkIkk">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 fw-semibold" id="tab-okk" data-bs-toggle="tab" data-bs-target="#panel-okk" type="button" role="tab">
                                <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">folder_open</i> OKK <span class="badge bg-success ms-1" id="badgeOkk">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 fw-semibold" id="tab-oak" data-bs-toggle="tab" data-bs-target="#panel-oak" type="button" role="tab">
                                <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">visibility</i> OAK <span class="badge bg-warning text-dark ms-1" id="badgeOak">0</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-4 bg-white" id="detailDopmTabContent">
                        <div class="tab-pane fade show active" id="panel-ipk-ikk" role="tabpanel">
                            <div id="ipkIkkLoading" class="text-center py-5 d-none bg-white">
                                <div class="spinner-border text-primary mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data IPK-IKK...</p>
                            </div>
                            <div id="ipkIkkEmpty" class="text-center py-5 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 56px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data IPK-IKK untuk kode IKK ini.</p>
                            </div>
                            <div id="ipkIkkTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2"><strong>Detail IPK-IKK</strong> — Tabel di bawah menampilkan seluruh data IPK-IKK dengan kode IKK ini.</p>
                                <div class="table-responsive modal-tab-table-scroll">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableIpkIkk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Durasi</th><th>CCTV</th><th>Kategori IJK</th><th>Status</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="panel-okk" role="tabpanel">
                            <div id="okkLoading" class="text-center py-5 d-none bg-white">
                                <div class="spinner-border text-success mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data OKK...</p>
                            </div>
                            <div id="okkEmpty" class="text-center py-5 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 56px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data OKK untuk kode IKK ini.</p>
                            </div>
                            <div id="okkTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2"><strong>Detail OKK</strong> — Tabel di bawah menampilkan seluruh data OKK dengan kode IKK ini.</p>
                                <div class="table-responsive modal-tab-table-scroll">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableOkk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Jenis IJK</th><th>Layer</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="panel-oak" role="tabpanel">
                            <div id="oakContext" class="card border mb-3 d-none bg-white">
                                <div class="card-body py-2 px-3">
                                    <small class="text-muted fw-semibold">Layer 2 / 3 / 4:</small>
                                    <span id="oakLayerNames" class="ms-1">—</span>
                                </div>
                            </div>
                            <div id="oakLoading" class="text-center py-5 d-none bg-white">
                                <div class="spinner-border text-warning mb-2" role="status"></div>
                                <p class="text-muted mb-0">Memuat data OAK...</p>
                            </div>
                            <div id="oakEmpty" class="text-center py-5 d-none bg-white">
                                <span class="material-icons-outlined text-muted" style="font-size: 56px;">inbox</span>
                                <p class="text-muted mt-2 mb-0">Tidak ada data OAK untuk kriteria ini.</p>
                            </div>
                            <div id="oakTableWrap" class="d-none bg-white">
                                <p class="small text-muted mb-2"><strong>Detail OAK</strong> — Tabel di bawah menampilkan data Observasi Area Kerja sesuai activity dan SID.</p>
                                <div class="table-responsive modal-tab-table-scroll">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="tableOak">
                                        <thead class="table-light"><tr><th>Activity</th><th>Sub Activity</th><th>Submit Date</th><th>Submit By</th><th>SID Pelapor</th><th>SID Team</th><th>Conclusion</th><th>Site</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Intervensi DOPM: 3 tab IPK-IKK, OKK, OAK + Layer 1 kirim WA pengingat IPK (Bootstrap modal full) --}}
    <div class="modal fade" id="intervensiDopmModal" tabindex="-1" aria-labelledby="intervensiDopmModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
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
                <div class="modal-body p-0">
                    <ul class="nav nav-pills nav-fill px-3 pt-3 pb-0 gap-2 border-bottom rounded-0" id="intervensiDopmTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-3 fw-semibold" id="intervensi-tab-ipk" data-bs-toggle="tab" data-bs-target="#intervensi-panel-ipk" type="button" role="tab">
                                <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">checklist</i> IPK-IKK <span class="badge bg-primary ms-1" id="intervensiBadgeIpk">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 fw-semibold" id="intervensi-tab-okk" data-bs-toggle="tab" data-bs-target="#intervensi-panel-okk" type="button" role="tab">
                                <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">folder_open</i> OKK <span class="badge bg-success ms-1" id="intervensiBadgeOkk">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-3 fw-semibold" id="intervensi-tab-oak" data-bs-toggle="tab" data-bs-target="#intervensi-panel-oak" type="button" role="tab">
                                <i class="material-icons-outlined align-middle me-1" style="font-size: 18px;">visibility</i> OAK <span class="badge bg-warning text-dark ms-1" id="intervensiBadgeOak">0</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-4 bg-white" id="intervensiDopmTabContent">
                        <div class="tab-pane fade show active" id="intervensi-panel-ipk" role="tabpanel">
                            {{-- Layer 1: Pengingat WA isi IPK (hanya tampil jika ada nama_layer_1) --}}
                            <div id="intervensiLayer1Wrap" class="card border-warning mb-4 d-none">
                                <div class="card-header bg-warning bg-opacity-10 py-2">
                                    <span class="material-icons-outlined align-middle me-1 text-warning">notifications_active</span>
                                    <strong>Layer 1 — Pengingat Isi IPK (INSPEKSI PRA KERJA)</strong>
                                </div>
                                <div class="card-body py-3">
                                    <p class="small text-muted mb-2">Kirim pengingat WA ke PIC Layer 1 untuk mengisi form IPK:</p>
                                    <div id="intervensiLayer1Users" class="d-flex flex-wrap gap-2"></div>
                                    <div id="intervensiLayer1Empty" class="text-muted small d-none">Tidak ada user terdaftar untuk Layer 1 ini.</div>
                                    <div id="intervensiLayer1Loading" class="text-muted small d-none">Memuat...</div>
                                </div>
                            </div>
                            <div id="intervensiIpkLoading" class="text-center py-4 d-none"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mb-0 mt-2">Memuat data IPK-IKK...</p></div>
                            <div id="intervensiIpkEmpty" class="text-center py-4 d-none"><span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span><p class="text-muted mt-2 mb-0">Tidak ada data IPK-IKK.</p></div>
                            <div id="intervensiIpkTableWrap" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableIpk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Durasi</th><th>CCTV</th><th>Kategori IJK</th><th>Status</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="intervensi-panel-okk" role="tabpanel">
                            <div id="intervensiOkkLoading" class="text-center py-4 d-none"><div class="spinner-border text-success" role="status"></div><p class="text-muted mb-0 mt-2">Memuat data OKK...</p></div>
                            <div id="intervensiOkkEmpty" class="text-center py-4 d-none"><span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span><p class="text-muted mt-2 mb-0">Tidak ada data OKK.</p></div>
                            <div id="intervensiOkkTableWrap" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOkk">
                                        <thead class="table-light"><tr><th>Waktu</th><th>Nama Pengawas</th><th>Kode SID</th><th>Kode IKK</th><th>Perusahaan</th><th>Site</th><th>Jenis IJK</th><th>Layer</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="intervensi-panel-oak" role="tabpanel">
                            <div id="intervensiOakLoading" class="text-center py-4 d-none"><div class="spinner-border text-warning" role="status"></div><p class="text-muted mb-0 mt-2">Memuat data OAK...</p></div>
                            <div id="intervensiOakEmpty" class="text-center py-4 d-none"><span class="material-icons-outlined text-muted" style="font-size: 48px;">inbox</span><p class="text-muted mt-2 mb-0">Tidak ada data OAK.</p></div>
                            <div id="intervensiOakTableWrap" class="d-none">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-striped align-middle mb-0 table-bordered" id="intervensiTableOak">
                                        <thead class="table-light"><tr><th>Activity</th><th>Sub Activity</th><th>Submit Date</th><th>Submit By</th><th>SID Pelapor</th><th>SID Team</th><th>Conclusion</th><th>Site</th></tr></thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
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
<script src="{{ URL::asset('build/js/index.js') }}"></script>
<script src="{{ URL::asset('build/plugins/peity/jquery.peity.min.js') }}"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
    var modalApiUrl = @json(route('dopmikk.api.ikk-modal-data'));
    var layer1UsersApiUrl = @json(route('dopmikk.api.layer1-users'));
    var ipkFormLink = 'https://docs.google.com/forms/d/e/1FAIpQLSddTpsj6qbXN3pSpHvhZvPJdM4CU10H3oY9k3MEg6NTzRublA/viewform';
    var modalEl = document.getElementById('detailDopmModal');
    var intervensiModalEl = document.getElementById('intervensiDopmModal');
    var intervensiModal = intervensiModalEl ? new bootstrap.Modal(intervensiModalEl) : null;
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);

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
            columnDefs: [{ targets: [8, 9], orderable: false }],
            dom: '<"row mb-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }

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

    // Saat ganti tab, pastikan konten (empty atau tabel) tampil
    var tabContainer = document.querySelector('#detailDopmModal #detailDopmTabs');
    if (tabContainer) {
        tabContainer.addEventListener('shown.bs.tab', function(ev) {
            var targetId = ev.target.getAttribute('data-bs-target');
            if (!targetId) return;
            var pane = document.getElementById(targetId.replace('#', ''));
            if (!pane) pane = document.querySelector(targetId);
            if (!pane) return;
            var visible = pane.querySelector('[id$="Empty"]:not(.d-none), [id$="TableWrap"]:not(.d-none)');
            if (visible) { visible.style.display = 'block'; visible.classList.remove('d-none'); }
        });
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
            var layer1Wrap = document.getElementById('intervensiLayer1Wrap');
            var layer1UsersEl = document.getElementById('intervensiLayer1Users');
            var layer1EmptyEl = document.getElementById('intervensiLayer1Empty');
            var layer1LoadingEl = document.getElementById('intervensiLayer1Loading');
            layer1Wrap.classList.add('d-none');
            layer1UsersEl.innerHTML = '';
            layer1EmptyEl.classList.add('d-none');
            layer1LoadingEl.classList.add('d-none');
            if (namaLayer1) {
                layer1Wrap.classList.remove('d-none');
                layer1LoadingEl.classList.remove('d-none');
                fetch(layer1UsersApiUrl + '?nama_layer_1=' + encodeURIComponent(namaLayer1), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        layer1LoadingEl.classList.add('d-none');
                        var users = (res && res.success && res.users) ? res.users : [];
                        var msg = namaLayer1 + ', anda harus mengisi INSPEKSI PRA KERJA (IPK)\\n' + ipkFormLink;
                        users.forEach(function(u) {
                            var num = normalizeWaNumber(u.selular);
                            if (!num) return;
                            var a = document.createElement('a');
                            a.href = 'https://wa.me/' + num + '?text=' + encodeURIComponent(namaLayer1 + ', anda harus mengisi INSPEKSI PRA KERJA (IPK)\n' + ipkFormLink);
                            a.target = '_blank';
                            a.rel = 'noopener';
                            a.className = 'btn btn-sm btn-success';
                            a.innerHTML = '<i class="material-icons-outlined me-1" style="font-size:16px;">send</i> Kirim WA ke ' + (u.nama || u.username || 'User');
                            layer1UsersEl.appendChild(a);
                        });
                        if (users.length === 0) layer1EmptyEl.classList.remove('d-none');
                    })
                    .catch(function() {
                        layer1LoadingEl.classList.add('d-none');
                        layer1EmptyEl.classList.remove('d-none');
                    });
            }
            intervensiModal.show();
            var params = new URLSearchParams({
                kode_ikk: data.kode_ikk || '',
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
                        var ipk = res.ipk_ikk || [], okk = res.okk || [], oak = res.oak || [];
                        document.getElementById('intervensiBadgeIpk').textContent = ipk.length;
                        document.getElementById('intervensiBadgeOkk').textContent = okk.length;
                        document.getElementById('intervensiBadgeOak').textContent = oak.length;
                        document.getElementById('intervensiIpkLoading').classList.add('d-none');
                        document.getElementById('intervensiOkkLoading').classList.add('d-none');
                        document.getElementById('intervensiOakLoading').classList.add('d-none');
                        if (ipk.length === 0) { document.getElementById('intervensiIpkEmpty').classList.remove('d-none'); } else {
                            document.getElementById('intervensiIpkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableIpk tbody');
                            if (tbody) { tbody.innerHTML = ''; ipk.forEach(function(r) {
                                tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.durasi_jam), safeStr(r.cctv_terekam), safeStr(r.kategori_ijk, 35), safeStr(r.status_pekerjaan)]));
                            }); }
                        }
                        if (okk.length === 0) { document.getElementById('intervensiOkkEmpty').classList.remove('d-none'); } else {
                            document.getElementById('intervensiOkkTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOkk tbody');
                            if (tbody) { tbody.innerHTML = ''; okk.forEach(function(r) {
                                tbody.appendChild(tr([formatTs(r.ts), safeStr(r.nama_pengawas), safeStr(r.kode_sid), safeStr(r.kode_ikk), safeStr(r.nama_perusahaan, 40), safeStr(r.site), safeStr(r.jenis_ijk, 35), safeStr(r.layer_pengawas)]));
                            }); }
                        }
                        if (oak.length === 0) { document.getElementById('intervensiOakEmpty').classList.remove('d-none'); } else {
                            document.getElementById('intervensiOakTableWrap').classList.remove('d-none');
                            var tbody = document.querySelector('#intervensiTableOak tbody');
                            if (tbody) { tbody.innerHTML = ''; oak.forEach(function(r) {
                                tbody.appendChild(tr([safeStr(r.activity), safeStr(r.sub_activity), safeStr(r.submit_date), safeStr(r.submit_by), safeStr(r.kode_sid_pelapor), safeStr(r.kode_sid_team), safeStr(r.conclusion, 50), safeStr(r.site)]));
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
            intervensiModalEl.addEventListener('shown.bs.modal', function onShown() {
                intervensiModalEl.removeEventListener('shown.bs.modal', onShown);
                doIntervensiFetch();
            }, { once: true });
            return;
        }

        var btn = e.target.closest('.btn-detail-dopm');
        if (!btn) return;
        var data = JSON.parse(btn.getAttribute('data-dopm') || '{}');
        var modalDoc = document.getElementById('detailDopmModal');
        document.getElementById('detailDopmTitle').textContent = (data.id_dop || 'Detail') + ' — ' + (data.nama_pekerjaan || 'DOPM').substring(0, 50);
        document.getElementById('detailDopmSubtitle').textContent = 'Kode IKK: ' + (data.kode_ikk || '—');
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
        modal.show();
        var params = new URLSearchParams({
            kode_ikk: data.kode_ikk || '',
            jenis_ijin_kerja_khusus: data.jenis_ijin_kerja_khusus || '',
            sid_layer_2: data.sid_layer_2 || '',
            sid_layer_3: data.sid_layer_3 || '',
            sid_layer_4: data.sid_layer_4 || '',
            nama_layer_2: data.nama_layer_2 || '',
            nama_layer_3: data.nama_layer_3 || '',
            nama_layer_4: data.nama_layer_4 || ''
        });
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
                var ctx = res.dopm_context || {};
                var layerNames = [ctx.nama_layer_2, ctx.nama_layer_3, ctx.nama_layer_4].filter(Boolean).join(' / ') || '—';
                document.getElementById('oakLayerNames').textContent = layerNames;
                document.getElementById('badgeIpkIkk').textContent = ipk.length;
                document.getElementById('badgeOkk').textContent = okk.length;
                document.getElementById('badgeOak').textContent = oak.length;
                document.getElementById('statCountIpkIkk').textContent = ipk.length;
                document.getElementById('statCountOkk').textContent = okk.length;
                document.getElementById('statCountOak').textContent = oak.length;
                if (layerNames !== '—') document.getElementById('oakContext').classList.remove('d-none');

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
                                safeStr(r.kode_sid_pelapor), safeStr(r.kode_sid_team), safeStr(r.conclusion, 50), safeStr(r.site)
                            ]));
                        });
                    }
                }
                // Paksa tampilkan wrap tabel di tab aktif (kadang Bootstrap belum update visibility)
                var activePane = modalDoc.querySelector('#detailDopmTabs + .tab-content .tab-pane.active');
                if (activePane) {
                    var wrap = activePane.querySelector('[id$="TableWrap"]');
                    if (wrap && !wrap.classList.contains('d-none')) {
                        wrap.style.display = 'block';
                        wrap.classList.remove('d-none');
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
        modalEl.addEventListener('shown.bs.modal', function onShown() {
            modalEl.removeEventListener('shown.bs.modal', onShown);
            doFetch();
        }, { once: true });
    });

})();
</script>
@endsection



