import React from 'react';
import {
  AbsoluteFill,
  Easing,
  Img,
  interpolate,
  staticFile,
  useCurrentFrame,
} from 'remotion';
import { Audio } from '@remotion/media';
import { PrecisionGrid } from '../components/PrecisionGrid';

export const Scene4Advantage: React.FC = () => {
  const frame = useCurrentFrame();

  const card1Progress = interpolate(frame, [15, 40], [0, 1], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  const card2Progress = interpolate(frame, [30, 55], [0, 1], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  const bannerProgress = interpolate(frame, [70, 95], [0, 1], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  const bgScale = interpolate(frame, [0, 410], [1.02, 1.12], {
    extrapolateRight: 'clamp',
  });

  return (
    <AbsoluteFill style={{ backgroundColor: '#090E17' }}>
      {/* Voiceover Track */}
      <Audio src={staticFile('audio/scene4.mp3')} volume={1} />

      {/* Background with Investor / Executive meeting aura */}
      <div
        style={{
          position: 'absolute',
          inset: 0,
          overflow: 'hidden',
        }}
      >
        <Img
          src={staticFile('images/inversores.jpg')}
          style={{
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            transform: `scale(${bgScale})`,
            filter: 'brightness(0.18) contrast(1.2) saturate(0.75)',
          }}
        />
        <div
          style={{
            position: 'absolute',
            inset: 0,
            background:
              'radial-gradient(circle at center, rgba(9, 14, 23, 0.6) 0%, rgba(9, 14, 23, 0.97) 100%)',
          }}
        />
      </div>

      <PrecisionGrid
        locationLabel="DUAL SEGMENT ARCHITECTURE"
        coordinates="B2C RESIDENTIAL · B2B INSTITUTIONAL"
        statusLabel="ASSESSMENT READY"
      />

      <div
        style={{
          position: 'absolute',
          inset: 0,
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          justifyContent: 'center',
          zIndex: 10,
          padding: '0 60px',
        }}
      >
        {/* Title */}
        <div style={{ textAlign: 'center', marginBottom: 28 }}>
          <span
            style={{
              fontSize: 13,
              letterSpacing: '0.22em',
              fontWeight: 700,
              color: '#BFA15F',
              textTransform: 'uppercase',
            }}
          >
            INSTITUTIONAL GRADE CERTAINTY FOR EVERY DECISION
          </span>
          <h2
            style={{
              fontSize: 46,
              fontWeight: 800,
              color: '#F2F5FA',
              letterSpacing: '-0.025em',
              margin: '8px 0 0 0',
            }}
          >
            Tailored For Families & Real Estate Investors
          </h2>
        </div>

        {/* Dual Cards */}
        <div
          style={{
            display: 'grid',
            gridTemplateColumns: '1fr 1fr',
            gap: 32,
            width: '100%',
            maxWidth: 1080,
            marginBottom: 28,
          }}
        >
          {/* Card 1: Families */}
          <div
            style={{
              opacity: card1Progress,
              transform: `translateY(${interpolate(card1Progress, [0, 1], [30, 0])}px)`,
              padding: '28px 32px',
              borderRadius: 10,
              backgroundColor: 'rgba(16, 22, 34, 0.8)',
              border: '1px solid rgba(191, 161, 95, 0.35)',
              boxShadow: '0 10px 30px rgba(0, 0, 0, 0.4)',
              backdropFilter: 'blur(10px)',
            }}
          >
            <div
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: 12,
                marginBottom: 14,
              }}
            >
              <span
                style={{
                  fontSize: 12,
                  fontWeight: 800,
                  letterSpacing: '0.18em',
                  color: '#BFA15F',
                  backgroundColor: 'rgba(191, 161, 95, 0.15)',
                  padding: '4px 10px',
                  borderRadius: 4,
                }}
              >
                PROFILE 01
              </span>
              <span
                style={{
                  fontSize: 18,
                  fontWeight: 700,
                  color: '#F2F5FA',
                }}
              >
                Families & Expat Relocation
              </span>
            </div>

            <p
              style={{
                fontSize: 15,
                color: '#A9B4C6',
                lineHeight: 1.5,
                marginBottom: 18,
              }}
            >
              Know exactly how it feels to live there before you pack. True neighborhood tranquility,
              safety after dark, hospital access, and social dynamics.
            </p>

            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
              {['Real neighborhood crime audit', 'Healthcare & international school access', 'Noise & environmental comfort'].map(
                (item, idx) => (
                  <div
                    key={idx}
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      gap: 10,
                      fontSize: 13,
                      color: '#EFE3C6',
                    }}
                  >
                    <span style={{ color: '#BFA15F', fontWeight: 800 }}>✓</span>
                    <span>{item}</span>
                  </div>
                )
              )}
            </div>
          </div>

          {/* Card 2: Investors */}
          <div
            style={{
              opacity: card2Progress,
              transform: `translateY(${interpolate(card2Progress, [0, 1], [30, 0])}px)`,
              padding: '28px 32px',
              borderRadius: 10,
              backgroundColor: 'rgba(16, 22, 34, 0.8)',
              border: '1px solid rgba(19, 91, 236, 0.4)',
              boxShadow: '0 10px 30px rgba(0, 0, 0, 0.4)',
              backdropFilter: 'blur(10px)',
            }}
          >
            <div
              style={{
                display: 'flex',
                alignItems: 'center',
                gap: 12,
                marginBottom: 14,
              }}
            >
              <span
                style={{
                  fontSize: 12,
                  fontWeight: 800,
                  letterSpacing: '0.18em',
                  color: '#7FA8F7',
                  backgroundColor: 'rgba(19, 91, 236, 0.2)',
                  padding: '4px 10px',
                  borderRadius: 4,
                }}
              >
                PROFILE 02
              </span>
              <span
                style={{
                  fontSize: 18,
                  fontWeight: 700,
                  color: '#F2F5FA',
                }}
              >
                Investors & Family Offices
              </span>
            </div>

            <p
              style={{
                fontSize: 15,
                color: '#A9B4C6',
                lineHeight: 1.5,
                marginBottom: 18,
              }}
            >
              Underwrite acquisitions with institutional precision. Objective cross-zone
              comparisons, tenant socioeconomic profiling, and official municipal planning.
            </p>

            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
              {['Rigorous urbanistic & zoning compliance', 'Socioeconomic profile & rental demand depth', 'Ten-year public infrastructure timeline'].map(
                (item, idx) => (
                  <div
                    key={idx}
                    style={{
                      display: 'flex',
                      alignItems: 'center',
                      gap: 10,
                      fontSize: 13,
                      color: '#EFE3C6',
                    }}
                  >
                    <span style={{ color: '#7FA8F7', fontWeight: 800 }}>✓</span>
                    <span>{item}</span>
                  </div>
                )
              )}
            </div>
          </div>
        </div>

        {/* Bottom Guarantee & Pricing Banner */}
        <div
          style={{
            opacity: bannerProgress,
            transform: `scale(${interpolate(bannerProgress, [0, 1], [0.95, 1])})`,
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            width: '100%',
            maxWidth: 1080,
            padding: '16px 28px',
            backgroundColor: 'rgba(191, 161, 95, 0.1)',
            border: '1px solid rgba(191, 161, 95, 0.35)',
            borderRadius: 8,
          }}
        >
          <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
            <span style={{ fontSize: 24 }}>🛡️</span>
            <div>
              <div style={{ fontSize: 14, fontWeight: 800, color: '#F2F5FA', letterSpacing: '0.08em' }}>
                COMPREHENSIVE INTELLIGENCE REPORTS FROM JUST €149
              </div>
              <div style={{ fontSize: 12, color: '#A9B4C6' }}>
                Full Express (€149), In-Depth Analysis (€349), and Dedicated Premium (€890).
              </div>
            </div>
          </div>

          <div
            style={{
              fontSize: 12,
              fontWeight: 700,
              letterSpacing: '0.14em',
              color: '#BFA15F',
              textTransform: 'uppercase',
              backgroundColor: 'rgba(0, 0, 0, 0.4)',
              padding: '6px 14px',
              borderRadius: 4,
              border: '1px solid rgba(191, 161, 95, 0.3)',
            }}
          >
            60-DAY "NEVER PAY TWICE" UPGRADE CREDIT
          </div>
        </div>
      </div>
    </AbsoluteFill>
  );
};
